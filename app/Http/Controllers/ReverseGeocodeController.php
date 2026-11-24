<?php

namespace App\Http\Controllers;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ReverseGeocodeController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        $coordinates = $request->validate([
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
        ]);
        $latitude = number_format((float) $coordinates['latitude'], 7, '.', '');
        $longitude = number_format((float) $coordinates['longitude'], 7, '.', '');
        $cacheKey = 'nominatim:'.hash('sha256', $latitude.','.$longitude);
        if ($address = Cache::get($cacheKey)) {
            return response()->json(['address' => $address]);
        }
        foreach ($this->providers() as $provider) {
            if ($provider === 'nominatim' && ! Cache::add('nominatim:request-limit', true, 2)) {
                return response()->json(['message' => 'Address lookup is busy. Please try again.'], 429);
            }

            try {
                $address = match ($provider) {
                    'nominatim' => $this->reverseWithNominatim($latitude, $longitude),
                    'openweather' => $this->reverseWithOpenWeather($latitude, $longitude),
                };
            } catch (ConnectionException $exception) {
                $this->logConnectionFailure($provider, $exception);

                continue;
            }

            if ($address !== null) {
                Cache::put($cacheKey, $address, now()->addDays(7));

                return response()->json(['address' => $address]);
            }
        }

        return response()->json(['message' => 'Address lookup is unavailable.'], 503);
    }

    /** @return list<string> */
    private function providers(): array
    {
        return array_values(array_unique(array_filter([
            config('services.reverse_geocoding.provider', 'nominatim'),
            config('services.reverse_geocoding.fallback', 'openweather'),
        ], fn (mixed $provider): bool => in_array($provider, ['nominatim', 'openweather'], true))));
    }

    private function reverseWithNominatim(string $latitude, string $longitude): ?string
    {
        $response = $this->httpClient()
            ->withHeaders([
                'Accept' => 'application/json',
                'User-Agent' => 'ClearWash/1.0 ('.config('app.url').')',
            ])
            ->get('https://nominatim.openstreetmap.org/reverse', [
                'format' => 'jsonv2', 'lat' => $latitude, 'lon' => $longitude, 'zoom' => 18,
            ]);
        $address = $response->json('display_name');

        if (! $response->successful()) {
            Log::warning('Nominatim reverse geocoding request failed.', ['status' => $response->status()]);

            return null;
        }
        if (! is_string($address) || $address === '') {
            Log::warning('Nominatim reverse geocoding response is missing display_name.', ['status' => $response->status()]);

            return null;
        }

        return $address;
    }

    private function reverseWithOpenWeather(string $latitude, string $longitude): ?string
    {
        $key = config('services.openweather.key');
        if (! is_string($key) || $key === '') {
            Log::warning('OpenWeather reverse geocoding is not configured.');

            return null;
        }

        $response = $this->httpClient()->get('https://api.openweathermap.org/geo/1.0/reverse', [
            'lat' => $latitude, 'lon' => $longitude, 'limit' => 1, 'appid' => $key,
        ]);
        $location = $response->json('0');

        if (! $response->successful()) {
            Log::warning('OpenWeather reverse geocoding request failed.', ['status' => $response->status()]);

            return null;
        }
        if (! is_array($location) || ! is_string($location['name'] ?? null) || $location['name'] === '') {
            Log::warning('OpenWeather reverse geocoding response is missing a location name.', ['status' => $response->status()]);

            return null;
        }

        return implode(', ', array_values(array_unique(array_filter([
            $location['name'],
            is_string($location['state'] ?? null) ? $location['state'] : null,
            is_string($location['country'] ?? null) ? $location['country'] : null,
        ]))));
    }

    private function httpClient(): PendingRequest
    {
        $options = ['version' => 1.1];
        if ($caBundle = config('services.reverse_geocoding.ca_bundle')) {
            $options['verify'] = $caBundle;
        }

        return Http::connectTimeout(3)->timeout(8)->withOptions($options);
    }

    private function logConnectionFailure(string $provider, ConnectionException $exception): void
    {
        $previous = $exception->getPrevious();
        preg_match('/cURL error (\d+)/', $exception->getMessage().' '.($previous?->getMessage() ?? ''), $matches);
        Log::warning(ucfirst($provider).' reverse geocoding connection failed.', [
            'exception' => $exception::class,
            'message' => $this->sanitizeExceptionMessage($exception->getMessage()),
            'previous_exception' => $previous ? $previous::class : null,
            'previous_message' => $previous ? $this->sanitizeExceptionMessage($previous->getMessage()) : null,
            'curl_error' => isset($matches[1]) ? (int) $matches[1] : null,
        ]);
    }

    private function sanitizeExceptionMessage(string $message): string
    {
        $message = preg_replace('~https?://\S+~i', '[URL redacted]', $message);
        $message = preg_replace('/\b(?:api[_-]?key|appid|token|password|secret|authorization)\s*[:=]\s*\S+/i', '[credential redacted]', $message);

        return mb_substr($message, 0, 500);
    }
}

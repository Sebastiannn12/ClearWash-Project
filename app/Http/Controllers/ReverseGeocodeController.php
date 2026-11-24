<?php

namespace App\Http\Controllers;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

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
        if (! Cache::add('nominatim:request-limit', true, 2)) {
            return response()->json(['message' => 'Address lookup is busy. Please try again.'], 429);
        }
        try {
            $client = Http::connectTimeout(3)->timeout(8)
                ->withHeaders(['User-Agent' => 'ClearWash/1.0 ('.config('app.url').')']);
            if ($caBundle = config('services.openweather.ca_bundle')) {
                $client = $client->withOptions(['verify' => $caBundle]);
            }
            $response = $client->get('https://nominatim.openstreetmap.org/reverse', [
                    'format' => 'jsonv2', 'lat' => $latitude, 'lon' => $longitude, 'zoom' => 18,
                ]);
            $address = $response->json('display_name');
            if ($response->successful() && is_string($address) && $address !== '') {
                Cache::put($cacheKey, $address, now()->addDays(7));

                return response()->json(['address' => $address]);
            }
        } catch (ConnectionException $exception) {
            return response()->json(['message' => 'Address lookup is unavailable.'], 503);
        }

        return response()->json(['message' => 'Address lookup is unavailable.'], 503);
    }
}

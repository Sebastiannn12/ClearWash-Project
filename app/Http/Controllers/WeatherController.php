<?php

namespace App\Http\Controllers;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WeatherController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        $coordinates = $request->validate([
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
        ]);
        $key = config('services.openweather.key');
        if (! $key) {
            return response()->json(['message' => 'Weather information is unavailable.'], 503);
        }

        try {
            $client = Http::connectTimeout(3)->timeout(8);
            if ($caBundle = config('services.openweather.ca_bundle')) {
                $client = $client->withOptions(['verify' => $caBundle]);
            }
            $response = $client->get('https://api.openweathermap.org/data/2.5/weather', [
                'lat' => $coordinates['latitude'], 'lon' => $coordinates['longitude'], 'appid' => $key, 'units' => 'metric',
            ]);
            if (! $response->successful() || ! is_numeric($response->json('main.temp')) || ! is_numeric($response->json('main.humidity')) || ! is_string($response->json('weather.0.description'))) {
                Log::warning('OpenWeather request failed or returned incomplete data.', ['status' => $response->status()]);
                return response()->json(['message' => 'Weather information is unavailable.'], 503);
            }
        } catch (ConnectionException $exception) {
            Log::warning('OpenWeather connection failed.');
            return response()->json(['message' => 'Weather information is unavailable.'], 503);
        }

        return response()->json(['temperature' => $response->json('main.temp'), 'humidity' => $response->json('main.humidity'), 'description' => $response->json('weather.0.description')]);
    }
}

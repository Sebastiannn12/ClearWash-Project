<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class ReverseGeocodeControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_nominatim_address_is_returned_and_requested_over_http_1_1(): void
    {
        config([
            'services.reverse_geocoding.provider' => 'nominatim',
            'services.reverse_geocoding.fallback' => null,
        ]);
        Http::preventStrayRequests();
        Http::fake([
            'nominatim.openstreetmap.org/*' => Http::response(['display_name' => 'San Mateo, Rizal, Philippines']),
        ]);

        $response = $this->actingAs(User::factory()->create())->getJson(route('location.reverse', [
            'latitude' => 14.6969,
            'longitude' => 121.1219,
        ]));

        $response->assertOk()->assertExactJson(['address' => 'San Mateo, Rizal, Philippines']);
        Http::assertSent(fn (Request $request): bool => str_starts_with($request->url(), 'https://nominatim.openstreetmap.org/reverse')
            && $request->toPsrRequest()->getProtocolVersion() === '1.1');
    }

    public function test_openweather_is_used_when_nominatim_connection_fails(): void
    {
        config([
            'services.openweather.key' => 'test-key',
            'services.reverse_geocoding.provider' => 'nominatim',
            'services.reverse_geocoding.fallback' => 'openweather',
        ]);
        Http::preventStrayRequests();
        Http::fake([
            'nominatim.openstreetmap.org/*' => Http::failedConnection('Nominatim unavailable'),
            'api.openweathermap.org/*' => Http::response([[
                'name' => 'San Mateo',
                'state' => 'Rizal',
                'country' => 'PH',
            ]]),
        ]);

        $response = $this->actingAs(User::factory()->create())->getJson(route('location.reverse', [
            'latitude' => 14.6969,
            'longitude' => 121.1219,
        ]));

        $response->assertOk()->assertExactJson(['address' => 'San Mateo, Rizal, PH']);
        Http::assertSentCount(2);
    }

    public function test_openweather_can_be_configured_as_the_primary_provider(): void
    {
        config([
            'services.openweather.key' => 'test-key',
            'services.reverse_geocoding.provider' => 'openweather',
            'services.reverse_geocoding.fallback' => null,
        ]);
        Http::preventStrayRequests();
        Http::fake([
            'api.openweathermap.org/*' => Http::response([[
                'name' => 'San Mateo',
                'state' => 'Rizal',
                'country' => 'PH',
            ]]),
        ]);

        $response = $this->actingAs(User::factory()->create())->getJson(route('location.reverse', [
            'latitude' => 14.6969,
            'longitude' => 121.1219,
        ]));

        $response->assertOk()->assertExactJson(['address' => 'San Mateo, Rizal, PH']);
        Http::assertSentCount(1);
        Http::assertSent(fn (Request $request): bool => str_starts_with($request->url(), 'https://api.openweathermap.org/geo/1.0/reverse'));
    }

    public function test_returns_503_when_all_reverse_geocoding_providers_fail(): void
    {
        config([
            'services.openweather.key' => 'test-key',
            'services.reverse_geocoding.provider' => 'nominatim',
            'services.reverse_geocoding.fallback' => 'openweather',
        ]);
        Http::preventStrayRequests();
        Http::fake([
            'nominatim.openstreetmap.org/*' => Http::response(['error' => 'Unavailable'], 503),
            'api.openweathermap.org/*' => Http::response([], 200),
        ]);

        $response = $this->actingAs(User::factory()->create())->getJson(route('location.reverse', [
            'latitude' => 14.6969,
            'longitude' => 121.1219,
        ]));

        $response->assertServiceUnavailable()->assertExactJson([
            'message' => 'Address lookup is unavailable.',
        ]);
        Http::assertSentCount(2);
    }
}

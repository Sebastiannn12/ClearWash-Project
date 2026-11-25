<?php

namespace Tests\Feature;

use App\Models\MembershipCategory;
use App\Models\MembershipLevel;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\TestWith;
use Tests\TestCase;

class MembershipSettingsAvailabilityTest extends TestCase
{
    use RefreshDatabase;

    #[TestWith(['categories', MembershipCategory::class])]
    #[TestWith(['levels', MembershipLevel::class])]
    public function test_default_settings_can_be_disabled_and_enabled(string $kind, string $model): void
    {
        $this->actingAs(User::factory()->create());
        $records = $model::whereIn('name', $model::REQUIRED_NAMES)->get();
        $this->assertCount(4, $records);
        $this->get(route("membership-settings.{$kind}.index"))->assertOk()
            ->assertSee('name="is_active" type="checkbox"', false);

        foreach ($records as $record) {
            foreach ([0, 1] as $active) {
                $payload = $record->only($kind === 'categories'
                    ? ['name', 'benefits', 'discount_percentage']
                    : ['name', 'benefits', 'fee', 'validity_days']);
                $this->patch(route("membership-settings.{$kind}.update", $record), [
                    ...$payload, 'is_active' => $active,
                ])->assertRedirect(route("membership-settings.{$kind}.index"))
                    ->assertSessionHasNoErrors()->assertSessionHas('success');

                $this->assertSame((bool) $active, $record->fresh()->is_active);
                $this->get(route('transactions.create'))->assertOk()
                    ->assertViewHas($kind, fn ($options): bool => $options->contains('id', $record->id) === (bool) $active);
            }
        }
    }
}

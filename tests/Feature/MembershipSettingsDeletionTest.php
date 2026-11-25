<?php

namespace Tests\Feature;

use App\Models\Member;
use App\Models\MembershipCategory;
use App\Models\MembershipLevel;
use App\Models\MembershipTransaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\TestWith;
use Tests\TestCase;

class MembershipSettingsDeletionTest extends TestCase
{
    use RefreshDatabase;

    public function test_all_unused_default_settings_have_dialogs_and_can_be_deleted(): void
    {
        $this->actingAs(User::factory()->create());
        foreach (['categories' => MembershipCategory::class, 'levels' => MembershipLevel::class] as $kind => $model) {
            $page = $this->get(route("membership-settings.{$kind}.index"))->assertOk();
            foreach ($model::whereIn('name', $model::REQUIRED_NAMES)->get() as $record) {
                $prefix = $kind === 'categories' ? 'category' : 'level';
                $page->assertSee('id="delete-'.$prefix.'-'.$record->id.'"', false);
                $this->delete(route("membership-settings.{$kind}.destroy", $record))
                    ->assertRedirect(route("membership-settings.{$kind}.index"))->assertSessionHas('success');
                $this->assertModelMissing($record);
            }
        }
    }

    #[TestWith(['categories', 'member'])]
    #[TestWith(['categories', 'trashed'])]
    #[TestWith(['categories', 'transaction'])]
    #[TestWith(['categories', 'legacy'])]
    #[TestWith(['levels', 'member'])]
    #[TestWith(['levels', 'trashed'])]
    #[TestWith(['levels', 'transaction'])]
    #[TestWith(['levels', 'legacy'])]
    public function test_referenced_settings_cannot_be_deleted(string $kind, string $reference): void
    {
        $this->actingAs(User::factory()->create());
        $category = MembershipCategory::where('name', 'Student')->firstOrFail();
        $level = MembershipLevel::where('name', 'Gold')->firstOrFail();
        $member = Member::create([
            'first_name' => 'Alex', 'last_name' => 'Reyes', 'phone' => '09171234567',
            'category' => 'Regular', 'membership_level' => 'Silver', 'joined_on' => now()->toDateString(),
            'membership_category_id' => MembershipCategory::where('name', 'Regular')->firstOrFail()->id,
            'membership_level_id' => MembershipLevel::where('name', 'Silver')->firstOrFail()->id,
        ]);
        $record = $kind === 'categories' ? $category : $level;
        if (in_array($reference, ['member', 'trashed'], true)) {
            $member->update($kind === 'categories'
                ? ['membership_category_id' => $category->id, 'category' => $category->name]
                : ['membership_level_id' => $level->id, 'membership_level' => $level->name]);
            if ($reference === 'trashed') {
                $member->delete();
            }
        } else {
            $transaction = MembershipTransaction::create([
                'member_id' => $member->id,
                'membership_level_id' => $kind === 'levels' && $reference !== 'legacy' ? $level->id : $member->membership_level_id,
                'membership_category_id' => $kind === 'categories' && $reference !== 'legacy' ? $category->id : null,
                'category_name' => $kind === 'categories' ? $category->name : 'Regular',
                'level_name' => $kind === 'levels' ? $level->name : 'Silver',
                'type' => 'Application', 'status' => 'Pending', 'amount_due' => 100, 'validity_days' => 30,
            ]);
        }

        $this->delete(route("membership-settings.{$kind}.destroy", $record))
            ->assertRedirect(route("membership-settings.{$kind}.index"))
            ->assertSessionHas('error', "{$record->name} cannot be deleted because it is referenced by members or transactions.");

        $this->assertModelExists($record);
        $this->assertSame(1, Member::withTrashed()->count());
        if (isset($transaction)) {
            $this->assertModelExists($transaction);
        }
    }

    public function test_guests_cannot_delete_settings(): void
    {
        $category = MembershipCategory::firstOrFail();
        $level = MembershipLevel::firstOrFail();

        $this->delete(route('membership-settings.categories.destroy', $category))->assertRedirect(route('login'));
        $this->delete(route('membership-settings.levels.destroy', $level))->assertRedirect(route('login'));

        $this->assertModelExists($category);
        $this->assertModelExists($level);
    }
}

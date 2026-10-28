<?php

namespace Tests\Feature;

use App\Models\Member;
use App\Models\MembershipLevel;
use App\Models\MembershipCategory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MemberManagementTest extends TestCase
{
    use RefreshDatabase;

    private function memberData(array $overrides = []): array
    {
        return array_merge([
            'first_name' => 'Alex',
            'last_name' => 'Reyes',
            'email' => 'alex@example.com',
            'phone' => '09171234567',
            'category' => 'Regular',
            'membership_level' => 'Silver',
            'joined_on' => now()->toDateString(),
        ], $overrides);
    }

    public function test_a_member_can_be_registered_and_gets_a_unique_number(): void
    {
        $this->post(route('members.store'), $this->memberData())->assertRedirect();
        $this->post(route('members.store'), $this->memberData(['email' => 'second@example.com']))->assertRedirect();

        $members = Member::orderBy('id')->get();
        $this->assertCount(2, $members);
        $this->assertNotNull($members[0]->membership_number);
        $this->assertNotEquals($members[0]->membership_number, $members[1]->membership_number);
        $this->assertTrue($members[0]->is_active);
    }

    public function test_member_details_are_validated(): void
    {
        $this->post(route('members.store'), $this->memberData([
            'first_name' => '',
            'membership_level' => 'Unknown',
            'joined_on' => now()->addDay()->toDateString(),
        ]))->assertSessionHasErrors(['first_name', 'membership_level', 'joined_on']);

        $this->assertDatabaseCount('members', 0);
    }

    public function test_member_can_be_updated_deactivated_and_reactivated(): void
    {
        $member = Member::create($this->memberData());

        $this->put(route('members.update', $member), $this->memberData(['first_name' => 'Jamie']))->assertRedirect(route('members.show', $member));
        $this->delete(route('members.destroy', $member))->assertRedirect();
        $this->assertFalse($member->fresh()->is_active);
        $this->patch(route('members.reactivate', $member))->assertRedirect();
        $this->assertTrue($member->fresh()->is_active);
        $this->assertSame('Jamie', $member->fresh()->first_name);
    }

    public function test_directory_can_filter_by_name_category_level_and_status(): void
    {
        Member::create($this->memberData());
        Member::create($this->memberData([
            'first_name' => 'Mina', 'last_name' => 'Santos',
            'category' => 'Corporate', 'membership_level' => 'Platinum',
            'email' => 'mina@example.com', 'is_active' => false,
        ]));

        $this->get(route('members.index', ['search' => 'Alex Reyes', 'category' => 'Regular', 'membership_level' => 'Silver', 'status' => 'active']))
            ->assertOk()->assertSee('Alex Reyes')->assertDontSee('Mina Santos');
    }

    public function test_membership_setup_lists_options_and_updates_level_details(): void
    {
        $this->get(route('membership-settings.index'))
            ->assertOk()
            ->assertSee('Student')
            ->assertSee('Senior Citizen')
            ->assertSee('Platinum')
            ->assertSee('Consolidated billing');

        $basic = MembershipLevel::where('name', 'Basic')->firstOrFail();
        $this->assertSame('299.00', $basic->fee);
        $this->assertSame(30, $basic->validity_days);
        $this->assertStringContainsString('2 standard car washes', $basic->benefits);

        $corporate = MembershipCategory::where('name', 'Corporate')->firstOrFail();
        $this->assertStringContainsString('vehicle record tracking', $corporate->benefits);

        $level = MembershipLevel::where('name', 'Gold')->firstOrFail();

        $this->patch(route('membership-settings.levels.update', $level), [
            'fee' => 799.50,
            'validity_days' => 90,
            'benefits' => 'Three premium washes and priority service.',
        ])->assertRedirect(route('membership-settings.index'));

        $this->assertDatabaseHas('membership_levels', [
            'id' => $level->id,
            'fee' => 799.50,
            'validity_days' => 90,
        ]);
    }

    public function test_member_can_be_permanently_deleted(): void
    {
        $member = Member::create($this->memberData());

        $this->delete(route('members.force-delete', $member))
            ->assertRedirect(route('members.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseMissing('members', ['id' => $member->id]);
    }
}

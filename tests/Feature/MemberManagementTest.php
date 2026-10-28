<?php

namespace Tests\Feature;

use App\Models\Member;
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
            'category' => 'Individual',
            'membership_level' => 'Plus',
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
            'category' => 'Corporate', 'membership_level' => 'Premium',
            'email' => 'mina@example.com', 'is_active' => false,
        ]));

        $this->get(route('members.index', ['search' => 'Alex Reyes', 'category' => 'Individual', 'membership_level' => 'Plus', 'status' => 'active']))
            ->assertOk()->assertSee('Alex Reyes')->assertDontSee('Mina Santos');
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

<?php

namespace Tests\Feature;

use App\Models\Member;
use App\Models\MembershipLevel;
use App\Models\MembershipTransaction;
use App\TransactionStatus;
use App\TransactionType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MemberRegistrationTransactionTest extends TestCase
{
    use RefreshDatabase;

    public function test_registering_a_member_creates_one_pending_application_with_current_plan_details(): void
    {
        $level = MembershipLevel::where('name', 'Silver')->firstOrFail();
        $level->update([
            'fee' => 625.50,
            'validity_days' => 45,
        ]);

        $this->post(route('members.store'), $this->memberData())
            ->assertRedirect();

        $member = Member::where('email', 'alex@example.com')->firstOrFail();
        $application = MembershipTransaction::where('member_id', $member->id)->sole();

        $this->assertSame(TransactionType::Application->value, $application->type);
        $this->assertSame(TransactionStatus::Pending->value, $application->status);
        $this->assertSame($level->id, $application->membership_level_id);
        $this->assertEquals(625.50, $application->amount_due);
        $this->assertSame(45, $application->validity_days);
        $this->assertNotNull($application->reference_number);
        $this->assertSame('Silver', $member->membership_level);
        $this->assertTrue($member->is_active);
    }

    public function test_updating_a_member_does_not_create_another_application(): void
    {
        $this->post(route('members.store'), $this->memberData());

        $member = Member::where('email', 'alex@example.com')->firstOrFail();

        $this->put(route('members.update', $member), $this->memberData([
            'first_name' => 'Jamie',
        ]))->assertRedirect(route('members.show', $member));

        $this->assertSame(1, $member->membershipTransactions()->count());
        $this->assertSame(TransactionType::Application->value, $member->membershipTransactions()->sole()->type);
    }

    public function test_a_duplicate_application_is_rejected_but_a_manual_renewal_is_allowed(): void
    {
        $this->post(route('members.store'), $this->memberData());

        $member = Member::where('email', 'alex@example.com')->firstOrFail();
        $level = MembershipLevel::where('name', 'Silver')->firstOrFail();

        $this->post(route('transactions.store'), [
            'member_id' => $member->id,
            'membership_level_id' => $level->id,
            'type' => TransactionType::Application->value,
        ])->assertSessionHasErrors('type');

        $this->assertSame(1, $member->membershipTransactions()->count());

        $this->post(route('transactions.store'), [
            'member_id' => $member->id,
            'membership_level_id' => $level->id,
            'type' => TransactionType::Renewal->value,
        ])->assertRedirect();

        $this->assertSame(2, $member->membershipTransactions()->count());
        $this->assertSame(1, $member->membershipTransactions()->where('type', TransactionType::Renewal->value)->count());
    }

    /**
     * @param  array<string, mixed>  $overrides
     * @return array<string, mixed>
     */
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
}

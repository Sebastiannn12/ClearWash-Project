<?php

namespace Tests\Feature;

use App\Models\Member;
use App\Models\MembershipLevel;
use App\Models\MembershipTransaction;
use App\TransactionStatus;
use App\TransactionType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MemberTransactionHistoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_member_details_display_complete_transaction_and_payment_history(): void
    {
        $member = $this->member('Alex', 'alex@example.com');
        $otherMember = $this->member('Mina', 'mina@example.com');
        $silver = MembershipLevel::where('name', 'Silver')->firstOrFail();
        $gold = MembershipLevel::where('name', 'Gold')->firstOrFail();

        $application = $this->transaction($member, $silver, [
            'type' => TransactionType::Application->value,
            'status' => TransactionStatus::Approve->value,
            'amount_due' => 499,
            'approved_at' => '2026-11-02 09:00:00',
            'expires_at' => '2026-12-02 09:00:00',
        ], '2026-11-01 08:30:00');
        $application->payments()->create([
            'payment_reference' => 'PAY-APPLICATION-001',
            'amount' => 499,
            'method' => 'Cash',
            'paid_at' => '2026-11-02 08:45:00',
        ]);

        $renewal = $this->transaction($member, $gold, [
            'type' => TransactionType::Renewal->value,
            'status' => TransactionStatus::Paid->value,
            'amount_due' => 799,
        ], '2026-11-18 14:15:00');
        $renewal->payments()->createMany([
            ['payment_reference' => 'PAY-RENEWAL-001', 'amount' => 300, 'method' => 'GCash', 'paid_at' => '2026-11-18 15:00:00'],
            ['payment_reference' => 'PAY-RENEWAL-002', 'amount' => 499, 'method' => 'Card', 'paid_at' => '2026-11-18 16:00:00'],
        ]);

        $cancelled = $this->transaction($member, $silver, [
            'type' => TransactionType::Renewal->value,
            'status' => TransactionStatus::Cancelled->value,
            'amount_due' => 499,
        ], '2026-11-19 10:00:00');

        $otherTransaction = $this->transaction($otherMember, $silver, [
            'type' => TransactionType::Application->value,
            'status' => TransactionStatus::Pending->value,
            'amount_due' => 499,
        ], '2026-11-20 10:00:00');

        $response = $this->get(route('members.show', $member));

        $response->assertOk()
            ->assertSee('Transaction history')
            ->assertSeeInOrder([$cancelled->reference_number, $renewal->reference_number, $application->reference_number])
            ->assertSee('Application · Silver')
            ->assertSee('Renewal · Gold')
            ->assertSee(TransactionStatus::Approve->value)
            ->assertSee(TransactionStatus::Paid->value)
            ->assertSee(TransactionStatus::Cancelled->value)
            ->assertSee('₱499.00')
            ->assertSee('₱799.00')
            ->assertSee('Nov 18, 2026 2:15 PM')
            ->assertSee('Dec 2, 2026 9:00 AM')
            ->assertSee('PAY-APPLICATION-001')
            ->assertSee('PAY-RENEWAL-001')
            ->assertSee('PAY-RENEWAL-002')
            ->assertSee('Cash')
            ->assertSee('GCash')
            ->assertSee('Card')
            ->assertSee('No payments recorded.')
            ->assertSee('href="'.route('transactions.show', $application).'"', false)
            ->assertSee('href="'.route('transactions.show', $renewal).'"', false)
            ->assertDontSee($otherTransaction->reference_number);
    }

    public function test_member_details_show_an_empty_state_when_no_transactions_exist(): void
    {
        $member = $this->member('Alex', 'alex@example.com');

        $response = $this->get(route('members.show', $member));

        $response->assertOk()
            ->assertSee('Transaction history')
            ->assertSee('No transactions recorded.');
    }

    private function member(string $firstName, string $email): Member
    {
        return Member::create([
            'first_name' => $firstName,
            'last_name' => 'Reyes',
            'email' => $email,
            'phone' => '09171234567',
            'category' => 'Regular',
            'membership_level' => 'Silver',
            'joined_on' => '2026-11-01',
        ]);
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    private function transaction(
        Member $member,
        MembershipLevel $level,
        array $attributes,
        string $createdAt,
    ): MembershipTransaction {
        $transaction = MembershipTransaction::create([
            'member_id' => $member->id,
            'membership_level_id' => $level->id,
            'validity_days' => 30,
            ...$attributes,
        ]);

        $transaction->forceFill(['created_at' => $createdAt, 'updated_at' => $createdAt])->saveQuietly();

        return $transaction;
    }
}

<?php

namespace Tests\Feature;

use App\Models\Member;
use App\Models\MembershipLevel;
use App\Models\MembershipTransaction;
use App\TransactionStatus;
use App\TransactionType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TransactionLifecycleTest extends TestCase
{
    use RefreshDatabase;

    public function test_approval_requires_full_payment_and_applies_the_plan_without_changing_member_record_status(): void
    {
        $transaction = $this->transaction();
        $transaction->member->update(['is_active' => false]);

        $this->patch(route('transactions.approve', $transaction))->assertSessionHasErrors('status');
        $this->assertNull($transaction->fresh()->approved_at);

        $this->pay($transaction, 499);
        $this->patch(route('transactions.approve', $transaction))->assertRedirect();

        $approved = $transaction->fresh();
        $this->assertSame(TransactionStatus::Approve->value, $approved->status);
        $this->assertSame('Gold', $approved->member->membership_level);
        $this->assertFalse($approved->member->is_active);
        $this->assertEquals(30, $approved->approved_at->diffInDays($approved->expires_at));

        $this->patch(route('transactions.approve', $transaction))->assertSessionHasErrors('status');
        $this->assertSame($approved->expires_at->toDateTimeString(), $transaction->fresh()->expires_at->toDateTimeString());
    }

    public function test_renewal_extends_from_existing_expiration_and_cancellation_preserves_payments(): void
    {
        $first = $this->transaction(TransactionType::Application->value);
        $this->pay($first, 499);
        $this->patch(route('transactions.approve', $first))->assertRedirect();

        $renewal = $this->transaction(TransactionType::Renewal->value, $first->member_id);
        $this->pay($renewal, 499);
        $this->patch(route('transactions.approve', $renewal))->assertRedirect();

        $this->assertSame(
            $first->fresh()->expires_at->addDays(30)->toDateTimeString(),
            $renewal->fresh()->expires_at->toDateTimeString()
        );

        $cancelled = $this->transaction(TransactionType::Renewal->value, $first->member_id);
        $this->pay($cancelled, 100);
        $this->patch(route('transactions.cancel', $cancelled))->assertRedirect();
        $this->assertSame(TransactionStatus::Cancelled->value, $cancelled->fresh()->status);
        $this->assertSame(1, $cancelled->payments()->count());
        $this->patch(route('transactions.cancel', $cancelled))->assertSessionHasErrors('status');
        $this->patch(route('transactions.cancel', $first))->assertSessionHasErrors('status');
    }

    public function test_expiration_marks_unfinished_and_ended_subscriptions_without_touching_member_status(): void
    {
        $pending = $this->transaction();
        $paid = $this->transaction();
        $this->pay($paid, 499);
        $approved = $this->transaction();
        $this->pay($approved, 499);
        $this->patch(route('transactions.approve', $approved))->assertRedirect();

        $this->travel(8)->days();
        $this->artisan('transactions:expire')->assertExitCode(0);
        $this->assertSame(TransactionStatus::Expired->value, $pending->fresh()->status);
        $this->assertSame(TransactionStatus::Expired->value, $paid->fresh()->status);
        $this->assertSame(TransactionStatus::Approve->value, $approved->fresh()->status);
        $this->patch(route('transactions.approve', $paid))->assertSessionHasErrors('status');

        $this->travel(23)->days();
        $this->artisan('transactions:expire')->assertExitCode(0);
        $this->assertSame(TransactionStatus::Expired->value, $approved->fresh()->status);
        $this->assertTrue($approved->fresh()->member->is_active);
        $this->assertSame(1, $approved->payments()->count());
    }

    private function transaction(string $type = TransactionType::Application->value, ?int $memberId = null): MembershipTransaction
    {
        $member = $memberId
            ? Member::findOrFail($memberId)
            : Member::create([
                'first_name' => 'Alex',
                'last_name' => 'Reyes',
                'phone' => '09171234567',
                'category' => 'Regular',
                'membership_level' => 'Silver',
                'joined_on' => now()->toDateString(),
            ]);

        return MembershipTransaction::create([
            'member_id' => $member->id,
            'membership_level_id' => MembershipLevel::where('name', 'Gold')->firstOrFail()->id,
            'type' => $type,
            'status' => TransactionStatus::Pending->value,
            'amount_due' => 499,
            'validity_days' => 30,
        ]);
    }

    private function pay(MembershipTransaction $transaction, int $amount): void
    {
        $this->post(route('transactions.payments.store', $transaction), [
            'payment_reference' => 'PAY-'.$transaction->id.'-'.$amount,
            'amount' => $amount,
            'method' => 'Cash',
        ])->assertRedirect();
    }
}

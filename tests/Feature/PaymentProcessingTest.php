<?php

namespace Tests\Feature;

use App\Models\Member;
use App\Models\MembershipLevel;
use App\Models\MembershipTransaction;
use App\TransactionStatus;
use App\TransactionType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\TestResponse;
use Tests\TestCase;

class PaymentProcessingTest extends TestCase
{
    use RefreshDatabase;

    public function test_partial_and_multiple_payments_update_totals_and_mark_the_transaction_paid_only_when_fully_paid(): void
    {
        $transaction = $this->createTransaction();

        $this->postPayment($transaction, 200, 'Cash', 'PAY-partial-1')->assertRedirect();

        $this->assertSame(TransactionStatus::Pending->value, $transaction->fresh()->status);
        $this->assertEquals(200, $transaction->payments()->sum('amount'));

        $this->get(route('transactions.show', $transaction))
            ->assertOk()
            ->assertSee('₱200.00')
            ->assertSee('₱299.00')
            ->assertSee('PAY-partial-1');

        $this->postPayment($transaction, 299, 'GCash', 'PAY-partial-2')->assertRedirect();

        $this->assertSame(TransactionStatus::Paid->value, $transaction->fresh()->status);
        $this->assertEquals(499, $transaction->payments()->sum('amount'));
        $this->assertSame(2, $transaction->payments()->count());
    }

    public function test_invalid_amounts_methods_and_overpayments_are_rejected(): void
    {
        $transaction = $this->createTransaction();

        $this->postPayment($transaction, 0, 'Cash', 'PAY-zero')->assertSessionHasErrors('amount');
        $this->postPayment($transaction, -1, 'Cash', 'PAY-negative')->assertSessionHasErrors('amount');
        $this->postPayment($transaction, 100, 'Cheque', 'PAY-method')->assertSessionHasErrors('method');
        $this->postPayment($transaction, 500, 'Cash', 'PAY-over')->assertSessionHasErrors('amount');

        $this->assertSame(0, $transaction->payments()->count());
        $this->assertSame(TransactionStatus::Pending->value, $transaction->fresh()->status);
    }

    public function test_a_duplicate_submission_is_recorded_only_once(): void
    {
        $transaction = $this->createTransaction();

        $this->postPayment($transaction, 100, 'Card', 'PAY-duplicate')->assertRedirect();
        $this->postPayment($transaction, 100, 'Card', 'PAY-duplicate')
            ->assertSessionHasErrors('payment_reference');

        $this->assertSame(1, $transaction->payments()->count());
        $this->assertEquals(100, $transaction->payments()->sum('amount'));
    }

    public function test_payments_are_rejected_for_closed_transactions(): void
    {
        foreach ([
            TransactionStatus::Approve->value,
            TransactionStatus::Expired->value,
            TransactionStatus::Cancelled->value,
        ] as $index => $status) {
            $transaction = $this->createTransaction($status);

            $this->postPayment($transaction, 100, 'Bank', "PAY-closed-{$index}")
                ->assertSessionHasErrors('amount');

            $this->assertSame(0, $transaction->payments()->count());
        }
    }

    public function test_all_supported_payment_methods_can_be_recorded(): void
    {
        foreach (['Cash', 'GCash', 'Card', 'Bank'] as $index => $method) {
            $transaction = $this->createTransaction();

            $this->postPayment($transaction, 499, $method, "PAY-method-{$index}")
                ->assertRedirect();

            $this->assertDatabaseHas('payments', [
                'membership_transaction_id' => $transaction->id,
                'method' => $method,
                'amount' => 499,
            ]);
        }
    }

    private function createTransaction(string $status = TransactionStatus::Pending->value): MembershipTransaction
    {
        $number = Member::count() + 1;
        $member = Member::create([
            'first_name' => 'Payment',
            'last_name' => "Member {$number}",
            'email' => "payment{$number}@example.com",
            'phone' => "0917000000{$number}",
            'category' => 'Regular',
            'membership_level' => 'Silver',
            'joined_on' => now()->toDateString(),
        ]);
        $level = MembershipLevel::where('name', 'Silver')->firstOrFail();

        return MembershipTransaction::create([
            'member_id' => $member->id,
            'membership_level_id' => $level->id,
            'type' => TransactionType::Renewal->value,
            'status' => $status,
            'amount_due' => 499,
            'validity_days' => 30,
        ]);
    }

    private function postPayment(
        MembershipTransaction $transaction,
        int|float $amount,
        string $method,
        string $reference,
    ): TestResponse {
        return $this->post(route('transactions.payments.store', $transaction), [
            'payment_reference' => $reference,
            'amount' => $amount,
            'method' => $method,
        ]);
    }
}

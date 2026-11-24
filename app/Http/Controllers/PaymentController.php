<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePaymentRequest;
use App\Models\MembershipTransaction;
use App\Models\Payment;
use App\Notifications\MembershipApproved;
use App\TransactionStatus;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\Validation\ValidationException;

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->validate(['search' => ['nullable', 'string', 'max:100']])['search'] ?? null;
        $transactions = MembershipTransaction::query()
            ->with(['member', 'payments'])
            ->where('status', TransactionStatus::Pending->value)
            ->when($search, fn ($query) => $query->where(function ($query) use ($search): void {
                $query->where('reference_number', 'like', "%{$search}%")
                    ->orWhereHas('member', fn ($memberQuery) => $memberQuery->where('first_name', 'like', "%{$search}%")->orWhere('last_name', 'like', "%{$search}%"));
            }))
            ->latest()
            ->paginate(10, ['*'], 'open_page')
            ->withQueryString();

        $payments = Payment::query()
            ->with(['membershipTransaction.member'])
            ->when($search, fn ($query) => $query->where(function ($query) use ($search): void {
                $query->where('payment_reference', 'like', "%{$search}%")
                    ->orWhereHas('membershipTransaction', fn ($transactionQuery) => $transactionQuery
                        ->where('reference_number', 'like', "%{$search}%")
                        ->orWhereHas('member', fn ($memberQuery) => $memberQuery
                            ->where('first_name', 'like', "%{$search}%")
                            ->orWhere('last_name', 'like', "%{$search}%")));
            }))
            ->orderByDesc('paid_at')
            ->orderByDesc('id')
            ->paginate(10, ['*'], 'history_page')
            ->withQueryString();

        return view('payments.index', compact('transactions', 'payments'));
    }

    public function create(MembershipTransaction $transaction)
    {
        return redirect()->route('transactions.show', $transaction)->withFragment('record-payment');
    }

    public function receipt(Payment $payment)
    {
        $payment->load(['membershipTransaction.member.membershipCategory', 'membershipTransaction.membershipLevel']);

        return view('payments.receipt', compact('payment'));
    }

    public function publicReceipt(Payment $payment)
    {
        $payment->load(['membershipTransaction.member.membershipCategory', 'membershipTransaction.membershipLevel']);

        return response()->view('payments.receipt', ['payment' => $payment, 'publicReceipt' => true])
            ->header('Cache-Control', 'private, no-store')
            ->header('Referrer-Policy', 'no-referrer');
    }

    public function store(StorePaymentRequest $request, MembershipTransaction $transaction): RedirectResponse
    {
        $validated = $request->validated();

        DB::transaction(function () use ($transaction, $validated): void {
            $lockedTransaction = MembershipTransaction::query()
                ->lockForUpdate()
                ->findOrFail($transaction->id);

            if (in_array($lockedTransaction->status, [
                TransactionStatus::Approve->value,
                TransactionStatus::Expired->value,
                TransactionStatus::Cancelled->value,
            ], true)) {
                throw ValidationException::withMessages([
                    'amount' => "Payments cannot be recorded for a {$lockedTransaction->status} transaction.",
                ]);
            }

            if ($lockedTransaction->created_at->addDays(7)->lte(now())) {
                throw ValidationException::withMessages(['amount' => 'This transaction has passed its seven-day payment window.']);
            }

            if ($lockedTransaction->payments()->where('payment_reference', $validated['payment_reference'])->exists()) {
                throw ValidationException::withMessages([
                    'payment_reference' => 'This payment submission was already recorded.',
                ]);
            }

            $amountDueInCents = (int) round((float) $lockedTransaction->amount_due * 100);
            $totalPaidInCents = (int) round((float) $lockedTransaction->payments()->sum('amount') * 100);
            $amountInCents = (int) round((float) $validated['amount'] * 100);
            $remainingInCents = max(0, $amountDueInCents - $totalPaidInCents);

            if ($amountInCents > $remainingInCents) {
                throw ValidationException::withMessages([
                    'amount' => 'The payment exceeds the remaining balance of ₱'.number_format($remainingInCents / 100, 2).'.',
                ]);
            }

            $payment = $lockedTransaction->payments()->create([
                'payment_reference' => $validated['payment_reference'],
                'amount' => number_format($amountInCents / 100, 2, '.', ''),
                'method' => $validated['method'],
                'paid_at' => now(),
                'notes' => $validated['notes'] ?? null,
            ]);

            if (
                $lockedTransaction->status === TransactionStatus::Pending->value
                && $totalPaidInCents + $amountInCents >= $amountDueInCents
            ) {
                $lockedTransaction->update(['status' => TransactionStatus::Paid->value]);
            }

            Notification::route('mail', $lockedTransaction->member->email)->notify(new MembershipApproved(
                $lockedTransaction, $totalPaidInCents + $amountInCents, $payment->id,
                $lockedTransaction->status === TransactionStatus::Paid->value ? 'Paid' : 'Partially Paid'
            ));
        });

        return redirect()
            ->route('transactions.show', $transaction)
            ->with('success', 'Payment recorded successfully.');
    }
}

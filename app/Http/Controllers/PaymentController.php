<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePaymentRequest;
use App\Models\MembershipTransaction;
use App\TransactionStatus;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->validate(['search' => ['nullable', 'string', 'max:100']])['search'] ?? null;
        $transactions = MembershipTransaction::query()
            ->with(['member', 'payments'])
            ->where('status', TransactionStatus::Pending->value)
            ->where('created_at', '>', now()->subDays(7))
            ->when($search, fn ($query) => $query->where(function ($query) use ($search): void {
                $query->where('reference_number', 'like', "%{$search}%")
                    ->orWhereHas('member', fn ($memberQuery) => $memberQuery->where('first_name', 'like', "%{$search}%")->orWhere('last_name', 'like', "%{$search}%"));
            }))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('payments.index', compact('transactions'));
    }

    public function create(MembershipTransaction $transaction)
    {
        $transaction->load(['member', 'payments']);
        $remainingBalance = max(0, (float) $transaction->amount_due - (float) $transaction->payments->sum('amount'));
        abort_unless($transaction->status === TransactionStatus::Pending->value && $remainingBalance > 0 && $transaction->created_at->addDays(7)->gt(now()), 404);
        $paymentSubmissionToken = 'PAY-'.Str::uuid();

        return view('payments.create', compact('transaction', 'remainingBalance', 'paymentSubmissionToken'));
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

            $lockedTransaction->payments()->create([
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
        });

        return redirect()
            ->route('transactions.show', $transaction)
            ->with('success', 'Payment recorded successfully.');
    }
}

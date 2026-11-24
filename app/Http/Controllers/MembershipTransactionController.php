<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMembershipTransactionRequest;
use App\Models\Member;
use App\Models\MembershipLevel;
use App\Models\MembershipTransaction;
use App\TransactionStatus;
use App\TransactionType;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use App\Notifications\MembershipApplicationSubmitted;
use App\Notifications\MembershipApproved;
use Illuminate\Support\Facades\Notification;

class MembershipTransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $transactions = MembershipTransaction::with(['member', 'membershipLevel'])
            ->latest()
            ->get();

        return view('transactions.index', compact('transactions'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $members = Member::with(['membershipLevel', 'membershipCategory'])->orderBy('first_name')->get();

        $levels = MembershipLevel::where('is_active', true)->get();

        return view('transactions.create', compact('members', 'levels'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreMembershipTransactionRequest $request)
    {
        $validated = $request->validated();

        $level = MembershipLevel::findOrFail(
            $validated['membership_level_id']
        );
        $member = Member::with('membershipCategory')->findOrFail($validated['member_id']);
        $category = $member->membershipCategory;

        if (! $category) {
            throw ValidationException::withMessages(['member_id' => 'This member has no membership category.']);
        }

        if (! $level->is_active) {
            throw ValidationException::withMessages([
                'membership_level_id' => 'This membership level is inactive.',
            ]);
        }

        $transaction = DB::transaction(function () use ($validated, $level, $category) {
            if (
                $validated['type'] === TransactionType::Application->value
                && MembershipTransaction::where('member_id', $validated['member_id'])
                ->where('type', TransactionType::Application->value)
                ->exists()
            ) {
                throw ValidationException::withMessages([
                    'type' => 'This member already has a membership application.',
                ]);
            }

            $pricing = MembershipTransaction::pricingFor($category, $level);

            return MembershipTransaction::create([
                'member_id' => $validated['member_id'],
                'membership_level_id' => $level->id,
                'type' => $validated['type'],
                'status' => (float) $pricing['amount_due'] === 0.0 ? TransactionStatus::Paid->value : TransactionStatus::Pending->value,
                ...$pricing,
                'category_name' => $category->name,
                'level_name' => $level->name,
                'validity_days' => $level->validity_days,
                'notes' => $validated['notes'] ?? null,
            ]);
        });

        if ($transaction->type === TransactionType::Application->value) {
            $transaction->load('member');

            Notification::route('mail', $transaction->member->email)
                ->notify(new MembershipApplicationSubmitted($transaction));
        }

        return redirect()
            ->route('transactions.show', $transaction)
            ->with('success', 'Membership transaction created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(MembershipTransaction $transaction)
    {
        $transaction->load([
            'member',
            'member.membershipCategory',
            'membershipLevel',
            'payments',
        ]);

        $totalPaid = (float) $transaction->payments->sum('amount');
        $remainingBalance = max(0, (float) $transaction->amount_due - $totalPaid);
        $paymentSubmissionToken = 'PAY-' . Str::uuid();

        return view('transactions.show', compact(
            'transaction',
            'totalPaid',
            'remainingBalance',
            'paymentSubmissionToken',
        ));
    }

    public function approve(MembershipTransaction $transaction): RedirectResponse
    {
        DB::transaction(function () use ($transaction): void {
            $member = Member::query()->lockForUpdate()->findOrFail($transaction->member_id);
            $lockedTransaction = MembershipTransaction::query()->lockForUpdate()->findOrFail($transaction->id);

            if ($lockedTransaction->status !== TransactionStatus::Paid->value) {
                throw ValidationException::withMessages(['status' => 'Only fully paid transactions can be approved.']);
            }

            if ($lockedTransaction->created_at->addDays(7)->lte(now())) {
                throw ValidationException::withMessages(['status' => 'This transaction has passed its seven-day approval window.']);
            }

            $dueInCents = (int) round((float) $lockedTransaction->amount_due * 100);
            $paidInCents = (int) round((float) $lockedTransaction->payments()->sum('amount') * 100);

            if ($paidInCents < $dueInCents) {
                throw ValidationException::withMessages(['status' => 'The remaining balance must be paid before approval.']);
            }

            $start = now();

            if ($lockedTransaction->type === TransactionType::Renewal->value) {
                $existingExpiration = MembershipTransaction::query()
                    ->where('member_id', $member->id)
                    ->where('id', '!=', $lockedTransaction->id)
                    ->whereNotNull('approved_at')
                    ->max('expires_at');

                if ($existingExpiration && $existingExpiration > $start->toDateTimeString()) {
                    $start = Carbon::parse($existingExpiration);
                }
            }

            $lockedTransaction->update([
                'status' => TransactionStatus::Approve->value,
                'approved_at' => now(),
                'expires_at' => $start->addDays($lockedTransaction->validity_days),
            ]);

            $member->update([
                'membership_level_id' => $lockedTransaction->membership_level_id,
                'membership_level' => $lockedTransaction->membershipLevel->name,
            ]);

            Notification::route('mail', $member->email)->notify(new MembershipApproved($lockedTransaction, $paidInCents));
        });

        return redirect()->route('transactions.show', $transaction)->with('success', 'Transaction approved.');
    }

    public function cancel(MembershipTransaction $transaction): RedirectResponse
    {
        DB::transaction(function () use ($transaction): void {
            $lockedTransaction = MembershipTransaction::query()->lockForUpdate()->findOrFail($transaction->id);

            if (! in_array($lockedTransaction->status, [TransactionStatus::Pending->value, TransactionStatus::Paid->value], true)) {
                throw ValidationException::withMessages(['status' => 'Only Pending or Paid transactions can be cancelled.']);
            }

            $lockedTransaction->update(['status' => TransactionStatus::Cancelled->value]);
        });

        return redirect()->route('transactions.show', $transaction)->with('success', 'Transaction cancelled. Payment history was retained.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(MembershipTransaction $membershipTransaction)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, MembershipTransaction $membershipTransaction)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(MembershipTransaction $membershipTransaction)
    {
        //
    }
}

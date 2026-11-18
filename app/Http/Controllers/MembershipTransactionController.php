<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMembershipTransactionRequest;
use App\Models\Member;
use App\Models\MembershipLevel;
use App\Models\MembershipTransaction;
use App\TransactionStatus;
use App\TransactionType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

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
        $members = Member::orderBy('first_name')->get();

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

        if (! $level->is_active) {
            throw ValidationException::withMessages([
                'membership_level_id' => 'This membership level is inactive.',
            ]);
        }

        $transaction = DB::transaction(function () use ($validated, $level) {
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

            return MembershipTransaction::create([
                'member_id' => $validated['member_id'],
                'membership_level_id' => $level->id,
                'type' => $validated['type'],
                'status' => TransactionStatus::Pending->value,
                'amount_due' => $level->fee,
                'validity_days' => $level->validity_days,
                'notes' => $validated['notes'] ?? null,
            ]);
        });

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
            'membershipLevel',
            'payments',
        ]);

        return view('transactions.show', compact('transaction'));
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

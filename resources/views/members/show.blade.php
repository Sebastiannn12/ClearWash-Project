@extends('layouts.app')

@section('title', $member->full_name)

@section('content')
    @if (session('error'))
        <div role="alert" class="mb-6 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm font-medium text-red-800">{{ session('error') }}</div>
    @endif

    <a href="{{ route('members.index') }}" class="inline-flex items-center rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 transition-colors hover:bg-slate-50">← Back to members</a>

    <div class="mt-5 flex flex-wrap items-start justify-between gap-4">
        <div>
            <p class="text-xs font-bold uppercase tracking-[0.15em] text-blue-800">Member profile</p>
            <h1 class="mt-2 text-3xl font-bold tracking-tight text-slate-950">{{ $member->full_name }}</h1>
            <div class="mt-3 flex flex-wrap items-center gap-3">
                <span class="font-mono text-sm font-semibold text-slate-600">{{ $member->membership_number }}</span>
                <span class="rounded-full px-2.5 py-1 text-xs font-bold {{ $member->is_active ? 'bg-emerald-50 text-emerald-800' : 'bg-slate-200 text-slate-800' }}">{{ $member->is_active ? 'Active' : 'Inactive' }}</span>
            </div>
        </div>
        <div class="flex flex-wrap items-center gap-3">
            <a href="{{ route('members.edit', $member) }}" class="inline-flex items-center rounded-lg bg-blue-700 px-4 py-2.5 text-sm font-bold text-white shadow-sm transition-colors hover:bg-blue-800">Edit Profile</a>
            <a href="{{ route('members.create') }}" class="inline-flex items-center rounded-lg bg-[#1E3A8A] px-4 py-2.5 text-sm font-bold text-white shadow-sm transition-colors hover:bg-blue-900">+ Add Member</a>
        </div>
    </div>

    <div class="mt-8 grid max-w-5xl gap-6 lg:grid-cols-[minmax(0,2fr)_minmax(250px,1fr)]">
        <section class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm sm:p-7">
            <h2 class="text-lg font-bold text-slate-950">Personal information</h2>
            <dl class="mt-6 grid gap-x-6 gap-y-6 sm:grid-cols-2">
                <div><dt class="text-xs font-bold uppercase tracking-wide text-slate-600">Full name</dt><dd class="mt-1 text-sm font-semibold text-slate-900">{{ $member->full_name }}</dd></div>
                <div><dt class="text-xs font-bold uppercase tracking-wide text-slate-600">Phone</dt><dd class="mt-1 text-sm font-semibold text-slate-900">{{ $member->phone }}</dd></div>
                <div><dt class="text-xs font-bold uppercase tracking-wide text-slate-600">Email</dt><dd class="mt-1 break-all text-sm font-semibold text-slate-900">{{ $member->email ?: '—' }}</dd></div>
                <div><dt class="text-xs font-bold uppercase tracking-wide text-slate-600">Address</dt><dd class="mt-1 whitespace-pre-line text-sm font-semibold text-slate-900">{{ $member->address ?: '—' }}</dd></div>
            </dl>
            <div class="mt-7 border-t border-slate-100 pt-6">
                <h3 class="text-sm font-bold text-slate-950">Notes</h3>
                <p class="mt-2 whitespace-pre-line text-sm font-medium leading-6 text-slate-700">{{ $member->notes ?: 'No notes added.' }}</p>
            </div>
        </section>

        <div class="space-y-6">
            <section class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm sm:p-7">
                <h2 class="text-lg font-bold text-slate-950">Membership</h2>
                <dl class="mt-5 space-y-5">
                    <div><dt class="text-xs font-bold uppercase tracking-wide text-slate-600">Number</dt><dd class="mt-1 font-mono text-sm font-semibold text-slate-900">{{ $member->membership_number }}</dd></div>
                    <div><dt class="text-xs font-bold uppercase tracking-wide text-slate-600">Category</dt><dd class="mt-1 text-sm font-semibold text-slate-900">{{ $member->category }}</dd></div>
                    <div><dt class="text-xs font-bold uppercase tracking-wide text-slate-600">Level</dt><dd class="mt-1 text-sm font-semibold text-slate-900">{{ $member->membership_level }}</dd></div>
                    <div><dt class="text-xs font-bold uppercase tracking-wide text-slate-600">Joined</dt><dd class="mt-1 text-sm font-semibold text-slate-900">{{ $member->joined_on->format('M j, Y') }}</dd></div>
                </dl>
            </section>

            <section class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm sm:p-7">
                <h2 class="text-lg font-bold text-slate-950">Account status</h2>
                <p class="mt-2 text-sm font-medium leading-6 text-slate-600">{{ $member->is_active ? 'Deactivating keeps this member’s records available.' : 'Reactivate this member to mark their account active again.' }}</p>
                <form method="POST" action="{{ $member->is_active ? route('members.destroy', $member) : route('members.reactivate', $member) }}" class="mt-5">
                    @csrf
                    @method($member->is_active ? 'DELETE' : 'PATCH')
                    <button type="submit" onclick="return confirm('Are you sure you want to {{ $member->is_active ? 'deactivate' : 'reactivate' }} this member?')" class="rounded-lg border px-4 py-2.5 text-sm font-bold {{ $member->is_active ? 'border-red-200 text-red-700 hover:bg-red-50' : 'border-blue-200 text-blue-800 hover:bg-blue-50' }}">{{ $member->is_active ? 'Deactivate member' : 'Reactivate member' }}</button>
                </form>
            </section>
        </div>
    </div>

    <section class="mt-6 max-w-5xl overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
        <div class="border-b border-slate-100 px-5 py-5 sm:px-7">
            <h2 class="text-lg font-bold text-slate-950">Transaction history</h2>
            <p class="mt-1 text-sm font-medium text-slate-600">Applications, renewals, and payments recorded for this member.</p>
        </div>

        @forelse ($member->membershipTransactions as $transaction)
            <article class="border-b border-slate-200 last:border-b-0">
                <div class="flex flex-wrap items-start justify-between gap-4 px-5 py-5 sm:px-7">
                    <div>
                        <a href="{{ route('transactions.show', $transaction) }}" class="font-mono text-sm font-bold text-blue-900 hover:underline">{{ $transaction->reference_number }}</a>
                        <p class="mt-1 text-sm font-semibold text-slate-800">{{ $transaction->type }} · {{ $transaction->membershipLevel->name }}</p>
                    </div>
                    <span class="rounded-full bg-blue-50 px-2.5 py-1 text-xs font-bold text-blue-800">{{ $transaction->status }}</span>
                </div>

                <dl class="grid gap-4 border-y border-slate-100 bg-slate-50/60 px-5 py-4 sm:grid-cols-2 sm:px-7 lg:grid-cols-4">
                    <div><dt class="text-xs font-bold uppercase tracking-wide text-slate-500">Amount</dt><dd class="mt-1 text-sm font-bold text-slate-900">₱{{ number_format((float) $transaction->amount_due, 2) }}</dd></div>
                    <div><dt class="text-xs font-bold uppercase tracking-wide text-slate-500">Created</dt><dd class="mt-1 text-sm font-semibold text-slate-800">{{ $transaction->created_at->format('M j, Y g:i A') }}</dd></div>
                    <div><dt class="text-xs font-bold uppercase tracking-wide text-slate-500">Approved</dt><dd class="mt-1 text-sm font-semibold text-slate-800">{{ $transaction->approved_at?->format('M j, Y g:i A') ?: '—' }}</dd></div>
                    <div><dt class="text-xs font-bold uppercase tracking-wide text-slate-500">Subscription ends</dt><dd class="mt-1 text-sm font-semibold text-slate-800">{{ $transaction->expires_at?->format('M j, Y g:i A') ?: '—' }}</dd></div>
                </dl>

                <div class="px-5 pt-5 sm:px-7">
                    <h3 class="text-sm font-bold text-slate-950">Payment history</h3>
                    <p class="mt-1 text-xs font-medium text-slate-500">Total paid: ₱{{ number_format((float) $transaction->payments->sum('amount'), 2) }}</p>
                </div>
                @include('transactions._payment-history', ['payments' => $transaction->payments])
            </article>
        @empty
            <div class="px-5 py-12 text-center sm:px-7">
                <p class="text-sm font-semibold text-slate-700">No transactions recorded.</p>
                <p class="mt-1 text-sm text-slate-500">Applications and renewals will appear here.</p>
            </div>
        @endforelse
    </section>
@endsection

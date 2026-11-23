@extends('layouts.app')

@section('title', 'Transaction history')

@section('content')
    <a href="{{ route('members.show', $member) }}" class="text-sm font-semibold text-brand-primary hover:underline">Back to member</a>
    <h1 class="mt-5 text-3xl font-bold tracking-tight text-slate-950">Transaction history</h1>
    <p class="mt-2 text-sm text-slate-600">{{ $member->full_name }} · {{ $member->membership_number }}</p>
    <section class="mt-6 max-w-5xl overflow-hidden rounded-2xl border border-brand-border bg-white">
        <div class="border-b border-brand-border/70 px-5 py-5 sm:px-7">
            <h2 class="text-lg font-bold text-slate-950">Transaction history</h2>
            <p class="mt-1 text-sm font-medium text-slate-600">Applications, renewals, and payments recorded for this member.</p>
        </div>

        @forelse ($member->membershipTransactions as $transaction)
            @php($statusClass = match ($transaction->status) {
                \App\TransactionStatus::Approve->value => 'bg-emerald-50 text-emerald-800',
                \App\TransactionStatus::Paid->value => 'bg-blue-50 text-blue-800',
                \App\TransactionStatus::Cancelled->value => 'bg-slate-100 text-slate-600',
                \App\TransactionStatus::Expired->value => 'bg-red-50 text-red-800',
                default => 'bg-amber-50 text-amber-800',
            })
            <article class="border-b border-brand-border last:border-b-0">
                <div class="flex flex-wrap items-start justify-between gap-4 px-5 py-5 sm:px-7">
                    <div>
                        <a href="{{ route('transactions.show', $transaction) }}" class="font-mono text-sm font-bold text-brand-primary hover:underline">{{ $transaction->reference_number }}</a>
                        <p class="mt-1 text-sm font-semibold text-slate-800">{{ $transaction->type }} · {{ $transaction->membershipLevel->name }}</p>
                    </div>
                    <span class="rounded-full px-2.5 py-1 text-xs font-bold {{ $statusClass }}">{{ $transaction->status }}</span>
                </div>

                <dl class="grid gap-4 border-y border-brand-border/70 bg-brand-canvas/60 px-5 py-4 sm:grid-cols-2 sm:px-7 lg:grid-cols-4">
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

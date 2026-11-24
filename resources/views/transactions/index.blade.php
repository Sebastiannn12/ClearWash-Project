@extends('layouts.app')

@section('title', 'Transactions')

@section('content')
    <div class="flex flex-wrap items-end justify-between gap-5">
        <div>
            <h1 class="text-3xl font-bold tracking-tight text-slate-950 sm:text-4xl">Transactions</h1>
            <p class="mt-2 text-sm leading-6 text-slate-600">Review membership applications, renewals, balances, and statuses.</p>
        </div>
    </div>

    <section class="mt-10" aria-labelledby="transaction-list-heading">
        <div class="flex items-end justify-between gap-4">
            <div><h2 id="transaction-list-heading" class="text-xl font-bold text-slate-950">Transaction records</h2><p class="mt-1 text-sm text-slate-600">Applications and renewals ordered by most recent.</p></div>
            <span class="text-sm font-semibold text-slate-500">{{ $transactions->count() }} records</span>
        </div>

        <div class="cw-ledger mt-4">
            @forelse ($transactions as $transaction)
                @php($statusClass = match ($transaction->status) {
                    \App\TransactionStatus::Approve->value => 'bg-emerald-50 text-emerald-800',
                    \App\TransactionStatus::Paid->value => 'bg-blue-50 text-blue-800',
                    \App\TransactionStatus::Cancelled->value => 'bg-slate-100 text-slate-600',
                    \App\TransactionStatus::Expired->value => 'bg-red-50 text-red-800',
                    default => 'bg-amber-50 text-amber-800',
                })
                <article class="cw-ledger-row cw-hover-record">
                    <span class="cw-ledger-icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M7 3h10a2 2 0 0 1 2 2v16l-3-2-4 2-4-2-3 2V5a2 2 0 0 1 2-2Z"/><path d="M8 8h8M8 12h6"/></svg></span>
                    <div class="min-w-0 flex-1">
                        <div class="flex flex-wrap items-center gap-2">
                            <p class="font-semibold text-slate-950">{{ $transaction->member->full_name }}</p>
                            <span class="rounded-full px-2.5 py-1 text-xs font-bold {{ $statusClass }}">{{ $transaction->status }}</span>
                        </div>
                        <p class="mt-1 break-all font-mono text-xs font-semibold text-brand-primary">{{ $transaction->reference_number }}</p>
                        <p class="mt-1 text-sm text-slate-600">{{ $transaction->type }} · {{ $transaction->level_name ?? $transaction->membershipLevel->name }} · {{ $transaction->created_at->format('M j, Y') }}</p>
                    </div>
                    <div class="flex w-full items-center justify-between gap-5 sm:w-auto sm:justify-end">
                        <strong class="whitespace-nowrap text-lg tabular-nums text-slate-950">₱{{ number_format((float) $transaction->amount_due, 2) }}</strong>
                        <a href="{{ route('transactions.show', $transaction) }}" class="cw-stretched-link inline-flex min-h-11 items-center text-sm font-bold text-brand-primary underline-offset-4" aria-label="View transaction {{ $transaction->reference_number }}">Details <span aria-hidden="true">→</span></a>
                    </div>
                </article>
            @empty
                <div class="px-6 py-12 text-center">
                    <p class="text-sm font-semibold text-slate-700">No transactions yet.</p>
                    <p class="mt-1 text-sm text-slate-500">Create a transaction to start an application or renewal.</p>
                </div>
            @endforelse
        </div>
    </section>
@endsection

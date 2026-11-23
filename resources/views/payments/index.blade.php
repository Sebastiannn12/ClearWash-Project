@extends('layouts.app')

@section('title', 'Record payments')

@section('content')
    <h1 class="text-3xl font-bold tracking-tight text-slate-950">Record payments</h1>
    <p class="mt-2 text-sm text-slate-600">Choose an open transaction to record a full or partial payment.</p>
    <form action="{{ route('payments.index') }}" method="GET" class="mt-6 flex flex-wrap gap-2">
        <label for="payment-search" class="sr-only">Search transactions or members</label>
        <input id="payment-search" name="search" value="{{ request('search') }}" placeholder="Search transaction or member" class="min-w-0 flex-1 rounded-xl border border-brand-border px-4 py-2.5 text-sm focus:border-brand-primary focus:ring-2 focus:ring-brand-border">
        <button class="rounded-xl bg-brand-primary px-4 py-2.5 text-sm font-bold text-white hover:bg-brand-sidebar">Search</button>
    </form>
    <div class="mt-6 divide-y divide-brand-border overflow-hidden rounded-2xl border border-brand-border bg-white">
        @forelse ($transactions as $transaction)
            @php($remaining = max(0, (float) $transaction->amount_due - (float) $transaction->payments->sum('amount')))
            @if ($remaining > 0)
                <article class="flex flex-wrap items-center justify-between gap-4 p-5">
                    <div class="min-w-0">
                        <p class="break-all font-mono text-sm font-bold text-brand-primary">{{ $transaction->reference_number }}</p>
                        <p class="mt-1 font-semibold text-slate-900">{{ $transaction->member->full_name }}</p>
                        <p class="mt-1 text-sm text-slate-600">Remaining: ₱{{ number_format($remaining, 2) }}</p>
                    </div>
                    <a href="{{ route('payments.create', $transaction) }}" class="rounded-xl bg-brand-primary px-4 py-2.5 text-sm font-bold text-white hover:bg-brand-sidebar">Record payment</a>
                </article>
            @endif
        @empty
            <p class="p-6 text-sm text-slate-600">No open transactions found.</p>
        @endforelse
    </div>
    <div class="mt-5">{{ $transactions->links() }}</div>
@endsection

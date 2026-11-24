@extends('layouts.app')

@section('title', 'Payments')

@section('content')
    <div class="flex flex-col gap-6 lg:flex-row lg:items-end lg:justify-between">
        <div>
            <h1 class="text-3xl font-bold tracking-tight text-slate-950">Payments</h1>
            <p class="mt-2 max-w-2xl text-sm leading-6 text-slate-600">Review outstanding balances and every payment recorded in ClearWash.</p>
        </div>
        <form action="{{ route('payments.index') }}" method="GET" class="flex w-full gap-2 lg:max-w-md">
            <label for="payment-search" class="sr-only">Search transactions, payments, or members</label>
            <input id="payment-search" name="search" value="{{ request('search') }}" placeholder="Search reference or member" class="min-w-0 flex-1 rounded-xl border border-brand-border px-4 py-2.5 text-sm focus:border-brand-primary focus:ring-2 focus:ring-brand-border">
            <button class="rounded-xl bg-brand-primary px-4 py-2.5 text-sm font-bold text-white hover:bg-brand-sidebar">Search</button>
        </form>
    </div>

    <section class="mt-10" aria-labelledby="open-balances-heading">
        <div class="flex items-end justify-between gap-4">
            <div><h2 id="open-balances-heading" class="text-xl font-bold text-slate-950">Unpaid and partially paid</h2><p class="mt-1 text-sm text-slate-600">Open balances that still need attention.</p></div>
            <span class="text-sm font-semibold text-slate-500">{{ $transactions->total() }} open</span>
        </div>
        <div class="cw-ledger mt-4">
            @forelse ($transactions as $transaction)
                @php($paid = (float) $transaction->payments->sum('amount'))
                @php($remaining = max(0, (float) $transaction->amount_due - $paid))
                @if ($remaining > 0)
                    <article class="cw-ledger-row cw-hover-record">
                        <span class="cw-ledger-icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="3" y="5" width="18" height="14" rx="3"/><path d="M3 10h18m-5 5h2"/></svg></span>
                        <div class="min-w-0 flex-1">
                            <p class="font-semibold text-slate-950">{{ $transaction->member->full_name }}</p>
                        <p class="mt-1 break-all font-mono text-xs font-semibold text-brand-primary">{{ $transaction->reference_number }}</p>
                            <p class="mt-1 text-sm text-slate-600">Due ₱{{ number_format((float) $transaction->amount_due, 2) }} · Paid ₱{{ number_format($paid, 2) }} · {{ $transaction->payments->isEmpty() ? 'Unpaid' : 'Partially paid' }}</p>
                        </div>
                        <div class="flex w-full items-center justify-between gap-4 sm:w-auto sm:justify-end">
                            <div class="sm:text-right"><p class="text-xs font-semibold text-slate-500">Remaining</p><p class="mt-1 text-lg font-bold tabular-nums text-slate-950">₱{{ number_format($remaining, 2) }}</p></div>
                            <a href="{{ route('transactions.show', $transaction) }}#record-payment" class="cw-stretched-link text-sm font-bold text-brand-primary underline-offset-4" aria-label="View balance for transaction {{ $transaction->reference_number }}">View <span aria-hidden="true">→</span></a>
                        </div>
                    </article>
                @endif
            @empty
                <div class="px-6 py-12 text-center"><p class="text-sm font-semibold text-slate-700">No open balances found.</p></div>
            @endforelse
        </div>
        <div class="mt-5">{{ $transactions->links() }}</div>
    </section>

    <section class="mt-12" aria-labelledby="payment-history-heading">
        <div><h2 id="payment-history-heading" class="text-xl font-bold text-slate-950">Payment history</h2><p class="mt-1 text-sm text-slate-600">Completed payment records and printable receipts.</p></div>
        <div class="cw-ledger mt-4">
            @forelse ($payments as $payment)
                <article class="cw-ledger-row cw-hover-record">
                    <span class="cw-ledger-icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M7 3h10a2 2 0 0 1 2 2v16l-3-2-4 2-4-2-3 2V5a2 2 0 0 1 2-2Z"/><path d="M8 8h8M8 12h6"/></svg></span>
                    <div class="min-w-0 flex-1">
                        <p class="font-semibold text-slate-950">{{ $payment->membershipTransaction->member->full_name }}</p>
                        <p class="mt-1 break-all font-mono text-xs font-semibold text-brand-primary">{{ $payment->membershipTransaction->reference_number }}</p>
                        <p class="mt-1 break-words text-sm text-slate-600">{{ $payment->method }} · {{ $payment->paid_at->format('M j, Y g:i A') }} · {{ $payment->payment_reference ?: 'No reference' }}</p>
                    </div>
                    <div class="flex w-full items-center justify-between gap-5 sm:w-auto sm:justify-end">
                        <strong class="whitespace-nowrap text-lg tabular-nums text-slate-950">₱{{ number_format((float) $payment->amount, 2) }}</strong>
                        <a href="{{ route('payments.receipt', $payment) }}" target="_blank" rel="noopener" class="cw-stretched-link inline-flex min-h-11 items-center text-sm font-bold text-brand-primary underline-offset-4" aria-label="Open receipt {{ $payment->payment_reference ?: 'for payment' }}">Receipt <span aria-hidden="true">→</span></a>
                    </div>
                </article>
            @empty
                <div class="px-6 py-12 text-center"><p class="text-sm font-semibold text-slate-700">No payments recorded.</p></div>
            @endforelse
        </div>
        <div class="mt-5">{{ $payments->links() }}</div>
    </section>
@endsection

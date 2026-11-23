@extends('layouts.app')

@section('title', $transaction->reference_number)

@section('content')
    @php($statusClass = match ($transaction->status) {
        \App\TransactionStatus::Approve->value => 'bg-emerald-50 text-emerald-800',
        \App\TransactionStatus::Paid->value => 'bg-blue-50 text-blue-800',
        \App\TransactionStatus::Cancelled->value => 'bg-slate-100 text-slate-600',
        \App\TransactionStatus::Expired->value => 'bg-red-50 text-red-800',
        default => 'bg-amber-50 text-amber-800',
    })
    <a href="{{ route('transactions.index') }}" class="inline-flex items-center gap-2 text-sm font-semibold text-brand-primary underline-offset-4 hover:underline focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-brand-accent"><svg class="size-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="m15 18-6-6 6-6"/></svg>Back to transactions</a>

    <div class="mt-5 flex flex-wrap items-start justify-between gap-4">
        <div>
            <h1 class="break-all font-mono text-2xl font-bold tracking-tight text-slate-950 sm:text-3xl">{{ $transaction->reference_number }}</h1>
            <div class="mt-3 flex flex-wrap items-center gap-3">
                <span class="text-sm font-semibold text-slate-600">{{ $transaction->type }}</span>
                <span class="rounded-full px-2.5 py-1 text-xs font-bold {{ $statusClass }}">{{ $transaction->status }}</span>
            </div>
        </div>
        @if (in_array($transaction->status, [\App\TransactionStatus::Pending->value, \App\TransactionStatus::Paid->value], true))
            <div class="flex flex-wrap gap-3">
                @if ($transaction->status === \App\TransactionStatus::Paid->value && $remainingBalance <= 0)
                    <form method="POST" action="{{ route('transactions.approve', $transaction) }}">
                        @csrf
                        @method('PATCH')
                        <button class="rounded-xl bg-brand-primary px-4 py-2.5 text-sm font-bold text-white transition-[background-color,transform] duration-150 hover:bg-brand-sidebar active:scale-[0.98]">Approve</button>
                    </form>
                @endif
                <form method="POST" action="{{ route('transactions.cancel', $transaction) }}" onsubmit="return confirm('Cancel this transaction? Payment history will be retained.')">
                    @csrf
                    @method('PATCH')
                    <button class="rounded-lg border border-red-200 px-4 py-2.5 text-sm font-bold text-red-700 hover:bg-red-50">Cancel</button>
                </form>
            </div>
        @endif
    </div>

    @error('status')
        <p class="mt-4 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm font-semibold text-red-700">{{ $message }}</p>
    @enderror

    <div class="mt-8 grid max-w-6xl gap-6 lg:grid-cols-[minmax(0,2fr)_minmax(280px,1fr)]">
        <div class="space-y-6">
            <section class="rounded-2xl border border-brand-border bg-white p-5 sm:p-7">
                <h2 class="text-lg font-bold text-slate-950">Transaction information</h2>
                <dl class="mt-6 grid gap-x-6 gap-y-6 sm:grid-cols-2">
                    <div>
                        <dt class="text-xs font-bold uppercase tracking-wide text-slate-600">Member</dt>
                        <dd class="mt-1 text-sm font-semibold text-slate-900">{{ $transaction->member->full_name }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-bold uppercase tracking-wide text-slate-600">Membership level</dt>
                        <dd class="mt-1 text-sm font-semibold text-slate-900">{{ $transaction->membershipLevel->name }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-bold uppercase tracking-wide text-slate-600">Transaction type</dt>
                        <dd class="mt-1 text-sm font-semibold text-slate-900">{{ $transaction->type }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-bold uppercase tracking-wide text-slate-600">Status</dt>
                        <dd class="mt-1 text-sm font-semibold text-slate-900">{{ $transaction->status }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-bold uppercase tracking-wide text-slate-600">Amount due</dt>
                        <dd class="mt-1 text-sm font-semibold text-slate-900">₱{{ number_format((float) $transaction->amount_due, 2) }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-bold uppercase tracking-wide text-slate-600">Validity</dt>
                        <dd class="mt-1 text-sm font-semibold text-slate-900">{{ $transaction->validity_days }} days</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-bold uppercase tracking-wide text-slate-600">Created</dt>
                        <dd class="mt-1 text-sm font-semibold text-slate-900">{{ $transaction->created_at->format('M j, Y g:i A') }}</dd>
                    </div>
                    @if ($transaction->approved_at)
                        <div><dt class="text-xs font-bold uppercase tracking-wide text-slate-600">Approved</dt><dd class="mt-1 text-sm font-semibold text-slate-900">{{ $transaction->approved_at->format('M j, Y g:i A') }}</dd></div>
                        <div><dt class="text-xs font-bold uppercase tracking-wide text-slate-600">Subscription ends</dt><dd class="mt-1 text-sm font-semibold text-slate-900">{{ $transaction->expires_at->format('M j, Y g:i A') }}</dd></div>
                    @endif
                </dl>

                <div class="mt-7 border-t border-brand-border/70 pt-6">
                    <h3 class="text-sm font-bold text-slate-950">Notes</h3>
                    <p class="mt-2 whitespace-pre-line text-sm font-medium leading-6 text-slate-700">{{ $transaction->notes ?: 'No notes added.' }}</p>
                </div>
            </section>

            <section class="overflow-hidden rounded-2xl border border-brand-border bg-white">
                <div class="border-b border-brand-border/70 px-5 py-5 sm:px-7">
                    <h2 class="text-lg font-bold text-slate-950">Payment history</h2>
                    <p class="mt-1 text-sm font-medium text-slate-600">Payments recorded for this transaction.</p>
                </div>

                @include('transactions._payment-history', ['payments' => $transaction->payments])
            </section>
        </div>

        <aside class="space-y-6">
            <section class="rounded-2xl border border-brand-border bg-white p-5 sm:p-7">
                <h2 class="text-lg font-bold text-slate-950">Payment summary</h2>
                <dl class="mt-5 space-y-5">
                    <div class="flex items-center justify-between gap-4"><dt class="text-sm font-semibold text-slate-600">Amount due</dt><dd class="text-sm font-bold text-slate-950">₱{{ number_format((float) $transaction->amount_due, 2) }}</dd></div>
                    <div class="flex items-center justify-between gap-4"><dt class="text-sm font-semibold text-slate-600">Total paid</dt><dd class="text-sm font-bold text-emerald-700">₱{{ number_format($totalPaid, 2) }}</dd></div>
                    <div class="flex items-center justify-between gap-4 border-t border-brand-border/70 pt-5"><dt class="text-sm font-bold text-slate-800">Remaining</dt><dd class="text-base font-bold text-slate-950">₱{{ number_format($remainingBalance, 2) }}</dd></div>
                </dl>
            </section>

            @if ($transaction->status === \App\TransactionStatus::Pending->value && $remainingBalance > 0)
                <a href="{{ route('payments.create', $transaction) }}" class="inline-flex rounded-xl bg-brand-primary px-4 py-3 text-sm font-bold text-white hover:bg-brand-sidebar">Record payment</a>
            @endif

            <section class="rounded-2xl border border-brand-border bg-white p-5 sm:p-7">
                <h2 class="text-lg font-bold text-slate-950">Reference</h2>
                <dl class="mt-5 space-y-5">
                    <div>
                        <dt class="text-xs font-bold uppercase tracking-wide text-slate-600">Transaction number</dt>
                        <dd class="mt-1 break-all font-mono text-sm font-semibold text-slate-900">{{ $transaction->reference_number }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-bold uppercase tracking-wide text-slate-600">Member number</dt>
                        <dd class="mt-1 font-mono text-sm font-semibold text-slate-900">{{ $transaction->member->membership_number ?: '—' }}</dd>
                    </div>
                </dl>
            </section>
        </aside>
    </div>
@endsection

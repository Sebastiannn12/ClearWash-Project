@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="flex flex-wrap items-end justify-between gap-5">
    <div>
        <h1 class="font-bold text-slate-950">Welcome back, {{ \Illuminate\Support\Str::before(auth()->user()->name, ' ') }}!</h1>
        <p class="mt-2 text-sm leading-6 text-slate-600">Here's what's happening with ClearWash today.</p>
    </div>
    <a href="{{ route('transactions.create') }}" class="inline-flex items-center gap-2 rounded-xl bg-brand-primary px-4 py-3 text-sm font-semibold text-white hover:bg-brand-sidebar">
        <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
            <path stroke-linecap="round" d="M12 5v14M5 12h14" />
        </svg>Create transaction
    </a>
</div>

<section class="mt-8 grid gap-5 lg:grid-cols-3" aria-label="ClearWash overview">
    <article class="cw-feature cw-hover-surface flex min-h-72 min-w-0 flex-col justify-between rounded-2xl p-6 sm:p-8 lg:col-span-2">
        <div class="flex items-start justify-between gap-4">
            <div>
                <h2 class="text-base font-semibold">Collected Payments</h2>
                <p class="cw-feature-muted mt-2 text-sm">Total recorded payments across ClearWash</p>
            </div>
            <span class="flex size-11 shrink-0 items-center justify-center rounded-xl border border-white/15 bg-white/5">
                <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                    <rect x="3" y="5" width="18" height="14" rx="3" />
                    <path d="M3 10h18m-5 5h2" />
                </svg>
            </span>
        </div>
        <div class="mt-10 flex flex-col">
            <p class="break-words text-4xl font-bold tracking-tight tabular-nums sm:text-5xl">₱{{ number_format((float) $totalCollected, 2) }}</p>
            <a href="{{ route('payments.index') }}" class="cw-stretched-link mt-6 self-end text-sm font-semibold text-[#f0cbd8] underline-offset-4">View payments <span aria-hidden="true">→</span></a>
        </div>
    </article>

    <article class="cw-kpi cw-hover-surface flex min-h-64 flex-col justify-between">
        <div class="flex items-start justify-between gap-3">
            <h2 class="max-w-36 text-sm font-semibold leading-6 text-slate-600">Pending transactions</h2>
            <span class="flex size-10 shrink-0 items-center justify-center rounded-xl bg-brand-canvas text-brand-primary">
                <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                    <circle cx="12" cy="12" r="9" />
                    <path stroke-linecap="round" d="M12 7v5l3 2" />
                </svg>
            </span>
        </div>
        <div class="mt-8 flex flex-col">
            <p class="text-5xl font-bold tracking-tight text-slate-950 tabular-nums">{{ number_format($pendingTransactions) }}</p><a href="{{ route('transactions.index') }}" class="cw-stretched-link mt-5 self-end text-sm font-semibold text-brand-primary underline-offset-4">Review transactions <span aria-hidden="true">→</span></a>
        </div>
    </article>

    <article class="cw-kpi cw-hover-surface flex flex-col justify-between lg:col-span-2">
        <div class="flex items-start justify-between gap-4">
            <div>
                <h2 class="text-sm font-semibold text-slate-600">Member directory</h2>
                <p class="mt-2 text-sm text-slate-500">Registered profiles and membership details</p>
            </div>
            <svg class="size-6 shrink-0 text-brand-primary" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                <circle cx="9" cy="8" r="3" />
                <path stroke-linecap="round" d="M3 20v-2a6 6 0 0 1 12 0v2M16 5a3 3 0 0 1 0 6m2 4a5 5 0 0 1 3 5" />
            </svg>
        </div>
        <div class="mt-8 flex flex-wrap items-end justify-between gap-5">
            <div>
                <p class="text-5xl font-bold tracking-tight text-slate-950 tabular-nums">{{ number_format($totalMembers) }}</p>
                <p class="mt-2 text-sm text-slate-500">Total members</p>
            </div>
            <a href="{{ route('members.index') }}" class="cw-stretched-link text-sm font-semibold text-brand-primary underline-offset-4">Manage members <span aria-hidden="true">→</span></a>
        </div>
    </article>

    <article class="cw-kpi cw-hover-surface flex flex-col justify-between">
        <div class="flex items-center justify-between gap-4">
            <h2 class="text-sm font-semibold text-slate-600">Active members</h2>
            <span class="flex size-8 items-center justify-center rounded-full bg-emerald-50 text-emerald-700"><svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m5 12 4 4L19 6" />
                </svg></span>
        </div>
        <div class="mt-8 flex flex-col">
            <p class="text-5xl font-bold tracking-tight text-slate-950 tabular-nums">{{ number_format($activeMembers) }}</p>
            <p class="mt-2 text-sm text-slate-500">Currently active profiles</p><a href="{{ route('members.index', ['status' => 'active']) }}" class="cw-stretched-link mt-5 self-end text-sm font-semibold text-brand-primary underline-offset-4">View active members <span aria-hidden="true">→</span></a>
        </div>
    </article>
</section>

<section class="mt-10" aria-labelledby="recent-transactions-heading">
    <div class="flex flex-wrap items-end justify-between gap-4">
        <h2 id="recent-transactions-heading" class="text-xl font-bold text-slate-950">Recent transactions</h2>
        <a href="{{ route('transactions.index') }}" class="text-sm font-semibold text-brand-primary underline-offset-4 hover:underline focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-brand-accent">View all transactions <span aria-hidden="true">→</span></a>
    </div>
    <div class="cw-ledger mt-4">
        @forelse ($recentTransactions as $transaction)
            @php($statusClass = match ($transaction->status) {
                \App\TransactionStatus::Approve->value => 'bg-emerald-50 text-emerald-800',
                \App\TransactionStatus::Paid->value => 'bg-blue-50 text-blue-800',
                \App\TransactionStatus::Cancelled->value => 'bg-slate-100 text-slate-600',
                \App\TransactionStatus::Expired->value => 'bg-red-50 text-red-800',
                default => 'bg-amber-50 text-amber-800',
            })
            <article class="cw-ledger-row cw-hover-record">
                <div class="min-w-0 flex-1">
                    <div class="flex flex-wrap items-center gap-2">
                        <p class="font-semibold text-slate-950">{{ $transaction->member?->full_name ?? 'Member unavailable' }}</p>
                        <span class="rounded-full px-2.5 py-1 text-xs font-bold {{ $statusClass }}">{{ $transaction->status }}</span>
                    </div>
                    <p class="mt-1 break-all font-mono text-xs font-semibold text-brand-primary">{{ $transaction->reference_number }}</p>
                    <p class="mt-1 text-sm text-slate-600">{{ $transaction->created_at->format('M j, Y') }}</p>
                </div>
                <div class="flex w-full items-center justify-between gap-5 sm:w-auto sm:justify-end">
                    <strong class="whitespace-nowrap text-lg tabular-nums text-slate-950">₱{{ number_format((float) $transaction->amount_due, 2) }}</strong>
                    <a href="{{ route('transactions.show', $transaction) }}" class="cw-stretched-link inline-flex min-h-11 items-center text-sm font-bold text-brand-primary underline-offset-4" aria-label="View transaction {{ $transaction->reference_number }}">Details <span aria-hidden="true">→</span></a>
                </div>
            </article>
        @empty
            <div class="px-6 py-12 text-center"><p class="text-sm font-semibold text-slate-700">No transactions yet.</p></div>
        @endforelse
    </div>
</section>
@endsection

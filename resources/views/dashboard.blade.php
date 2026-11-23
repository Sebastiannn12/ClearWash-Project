@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <div class="max-w-2xl">
        <h1 class="text-3xl font-bold tracking-tight text-slate-950 sm:text-4xl">Dashboard</h1>
        <p class="mt-3 text-sm font-medium leading-6 text-slate-600 sm:text-base">A current snapshot of members, transactions, and recorded payments.</p>
    </div>

    <section class="mt-7 sm:mt-9" aria-labelledby="key-metrics-heading">
        <div class="mb-4 flex items-end justify-between gap-4">
            <div>
                <h2 id="key-metrics-heading" class="text-base font-bold text-slate-950">Key metrics</h2>
                <p class="mt-1 text-sm text-slate-500">Current totals across ClearWash.</p>
            </div>
        </div>

        <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <article class="flex min-h-40 flex-col justify-between rounded-2xl border border-brand-border bg-white p-5 sm:p-6">
                <div class="flex items-start justify-between gap-4">
                    <p class="text-sm font-semibold text-slate-600">Total members</p>
                    <span class="flex size-9 shrink-0 items-center justify-center rounded-xl bg-brand-accent/10 text-brand-primary" aria-hidden="true">
                        <svg class="size-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 19a4 4 0 0 0-8 0m8 0H8m8 0h3a2 2 0 0 0 2-2v-1a4 4 0 0 0-3-3.87M8 19H5a2 2 0 0 1-2-2v-1a4 4 0 0 1 3-3.87M12 12a4 4 0 1 0 0-8 4 4 0 0 0 0 8Z"/></svg>
                    </span>
                </div>
                <p class="mt-6 text-3xl font-bold tracking-tight text-slate-950 tabular-nums">{{ number_format($totalMembers) }}</p>
            </article>

            <article class="flex min-h-40 flex-col justify-between rounded-2xl border border-brand-border bg-brand-accent/10 p-5 sm:p-6">
                <div class="flex items-start justify-between gap-4">
                    <p class="text-sm font-semibold text-brand-sidebar">Active members</p>
                    <span class="flex size-9 shrink-0 items-center justify-center rounded-xl bg-white text-brand-primary" aria-hidden="true">
                        <svg class="size-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m7.5 12 3 3 6-6M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
                    </span>
                </div>
                <p class="mt-6 text-3xl font-bold tracking-tight text-brand-sidebar tabular-nums">{{ number_format($activeMembers) }}</p>
            </article>

            <article class="flex min-h-40 flex-col justify-between rounded-2xl border border-brand-border bg-white p-5 sm:p-6">
                <div class="flex items-start justify-between gap-4">
                    <p class="text-sm font-semibold text-slate-600">Pending transactions</p>
                    <span class="flex size-9 shrink-0 items-center justify-center rounded-xl bg-slate-100 text-slate-700" aria-hidden="true">
                        <svg class="size-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6l4 2m5-2a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
                    </span>
                </div>
                <p class="mt-6 text-3xl font-bold tracking-tight text-slate-950 tabular-nums">{{ number_format($pendingTransactions) }}</p>
            </article>

            <article class="flex min-h-40 min-w-0 flex-col justify-between rounded-2xl bg-brand-sidebar p-5 text-white sm:p-6">
                <div class="flex items-start justify-between gap-4">
                    <p class="text-sm font-semibold text-brand-border">Total collected payments</p>
                    <span class="flex size-9 shrink-0 items-center justify-center rounded-xl bg-white/10 text-brand-border" aria-hidden="true">
                        <svg class="size-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 7h14M5 12h14M5 17h8M4 3h16a1 1 0 0 1 1 1v16a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V4a1 1 0 0 1 1-1Z"/></svg>
                    </span>
                </div>
                <p class="mt-6 break-words text-2xl font-bold tracking-tight text-white tabular-nums sm:text-3xl">₱{{ number_format((float) $totalCollected, 2) }}</p>
            </article>
        </div>
    </section>
@endsection

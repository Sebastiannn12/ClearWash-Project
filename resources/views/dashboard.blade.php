@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <div class="flex flex-wrap items-end justify-between gap-5">
        <div>
            <h1 class="font-bold text-slate-950">Welcome back, {{ \Illuminate\Support\Str::before(auth()->user()->name, ' ') }}!</h1>
            <p class="mt-2 text-sm leading-6 text-slate-600">Here's what's happening with ClearWash today.</p>
        </div>
        <a href="{{ route('transactions.create') }}" class="inline-flex items-center gap-2 rounded-xl bg-brand-primary px-4 py-3 text-sm font-semibold text-white hover:bg-brand-sidebar">
            <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" d="M12 5v14M5 12h14"/></svg>Create transaction
        </a>
    </div>

    <section class="mt-8 grid gap-5 md:grid-cols-3" aria-label="ClearWash overview">
        <article class="cw-feature flex min-h-72 min-w-0 flex-col justify-between rounded-2xl p-6 sm:p-8 md:col-span-2">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <h2 class="text-base font-semibold">Collected Payments</h2>
                    <p class="cw-feature-muted mt-2 text-sm">Total recorded payments across ClearWash</p>
                </div>
                <span class="flex size-11 shrink-0 items-center justify-center rounded-xl border border-white/15 bg-white/5">
                    <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><rect x="3" y="5" width="18" height="14" rx="3"/><path d="M3 10h18m-5 5h2"/></svg>
                </span>
            </div>
            <div class="mt-10">
                <p class="break-words text-4xl font-bold tracking-tight tabular-nums sm:text-5xl">₱{{ number_format((float) $totalCollected, 2) }}</p>
                <a href="{{ route('payments.index') }}" class="mt-6 inline-flex items-center gap-2 text-sm font-semibold underline-offset-4 hover:underline">View payments <span aria-hidden="true">→</span></a>
            </div>
        </article>

        <article class="cw-kpi flex min-h-64 flex-col justify-between">
            <div class="flex items-start justify-between gap-3">
                <h2 class="max-w-36 text-sm font-semibold leading-6 text-slate-600">Pending transactions</h2>
                <span class="flex size-10 shrink-0 items-center justify-center rounded-xl bg-brand-canvas text-brand-primary">
                    <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path stroke-linecap="round" d="M12 7v5l3 2"/></svg>
                </span>
            </div>
            <div class="mt-8"><p class="text-5xl font-bold tracking-tight text-slate-950 tabular-nums">{{ number_format($pendingTransactions) }}</p><a href="{{ route('transactions.index') }}" class="mt-5 inline-flex text-sm font-semibold text-brand-primary hover:underline">Review transactions →</a></div>
        </article>

        <article class="cw-kpi flex flex-col justify-between md:col-span-2">
            <div class="flex items-start justify-between gap-4">
                <div><h2 class="text-sm font-semibold text-slate-600">Member directory</h2><p class="mt-2 text-sm text-slate-500">Registered profiles and membership details</p></div>
                <svg class="size-6 shrink-0 text-brand-primary" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><circle cx="9" cy="8" r="3"/><path stroke-linecap="round" d="M3 20v-2a6 6 0 0 1 12 0v2M16 5a3 3 0 0 1 0 6m2 4a5 5 0 0 1 3 5"/></svg>
            </div>
            <div class="mt-8 flex flex-wrap items-end justify-between gap-5">
                <div><p class="text-5xl font-bold tracking-tight text-slate-950 tabular-nums">{{ number_format($totalMembers) }}</p><p class="mt-2 text-sm text-slate-500">Total members</p></div>
                <a href="{{ route('members.index') }}" class="rounded-xl border border-brand-border px-4 py-3 text-sm font-semibold text-slate-800 hover:bg-brand-canvas">Manage members →</a>
            </div>
        </article>

        <article class="cw-kpi flex flex-col justify-between">
            <div class="flex items-center justify-between gap-4">
                <h2 class="text-sm font-semibold text-slate-600">Active members</h2>
                <span class="flex size-8 items-center justify-center rounded-full bg-emerald-50 text-emerald-700"><svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="m5 12 4 4L19 6"/></svg></span>
            </div>
            <div class="mt-8"><p class="text-5xl font-bold tracking-tight text-slate-950 tabular-nums">{{ number_format($activeMembers) }}</p><p class="mt-2 text-sm text-slate-500">Currently active profiles</p></div>
        </article>
    </section>
@endsection

@extends('layouts.app')

@section('title', $member->full_name)

@section('content')
    @if (session('error'))
        <div role="alert" class="mb-6 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm font-medium text-red-800">{{ session('error') }}</div>
    @endif

    <a href="{{ route('members.index') }}" class="inline-flex items-center gap-2 text-sm font-semibold text-brand-primary underline-offset-4 hover:underline focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-brand-accent"><svg class="size-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="m15 18-6-6 6-6"/></svg>Back to members</a>

    <div class="mt-5 flex flex-wrap items-start justify-between gap-4">
        <div>
            <h1 class="text-3xl font-bold tracking-tight text-slate-950 sm:text-4xl">{{ $member->full_name }}</h1>
            <div class="mt-3 flex flex-wrap items-center gap-3">
                <span class="font-mono text-sm font-semibold text-slate-600">{{ $member->membership_number }}</span>
                <span class="rounded-full px-2.5 py-1 text-xs font-bold {{ $member->is_active ? 'bg-emerald-50 text-emerald-800' : 'bg-slate-200 text-slate-800' }}">{{ $member->is_active ? 'Active' : 'Inactive' }}</span>
            </div>
        </div>
        <div class="flex flex-wrap items-center gap-3">
            <a href="{{ route('members.print', $member) }}" target="_blank" rel="noopener" class="inline-flex items-center rounded-xl border border-brand-border bg-white px-4 py-2.5 text-sm font-bold text-brand-primary hover:bg-brand-canvas">Print record</a>
            <a href="{{ route('members.edit', $member) }}" class="inline-flex items-center rounded-xl border border-brand-border bg-brand-accent/10 px-4 py-2.5 text-sm font-bold text-brand-primary transition-[background-color,transform] duration-150 hover:bg-brand-accent/15 active:scale-[0.98]">Edit profile</a>
            <a href="{{ route('members.create') }}" class="inline-flex items-center rounded-xl bg-brand-primary px-4 py-2.5 text-sm font-bold text-white transition-[background-color,transform] duration-150 hover:bg-brand-sidebar active:scale-[0.98]">Add member</a>
        </div>
    </div>

    <div class="mt-8 grid max-w-5xl gap-6 lg:grid-cols-[minmax(0,2fr)_minmax(250px,1fr)]">
        <section class="cw-hover-surface rounded-2xl border border-brand-border bg-white p-5 sm:p-7">
            <h2 class="text-lg font-bold text-slate-950">Personal information</h2>
            <dl class="mt-6 grid gap-x-6 gap-y-6 sm:grid-cols-2">
                <div><dt class="text-xs font-bold uppercase tracking-wide text-slate-600">Full name</dt><dd class="mt-1 text-sm font-semibold text-slate-900">{{ $member->full_name }}</dd></div>
                <div><dt class="text-xs font-bold uppercase tracking-wide text-slate-600">Phone</dt><dd class="mt-1 text-sm font-semibold text-slate-900">{{ $member->phone }}</dd></div>
                <div><dt class="text-xs font-bold uppercase tracking-wide text-slate-600">Email</dt><dd class="mt-1 break-all text-sm font-semibold text-slate-900">{{ $member->email ?: '—' }}</dd></div>
                <div><dt class="text-xs font-bold uppercase tracking-wide text-slate-600">Address</dt><dd class="mt-1 whitespace-pre-line text-sm font-semibold text-slate-900">{{ $member->address ?: '—' }}</dd></div>
            </dl>
            <div class="mt-7 border-t border-brand-border/70 pt-6">
                <h3 class="text-sm font-bold text-slate-950">Notes</h3>
                <p class="mt-2 whitespace-pre-line text-sm font-medium leading-6 text-slate-700">{{ $member->notes ?: 'No notes added.' }}</p>
            </div>
        </section>

        <div class="space-y-6">
            <section class="cw-hover-surface rounded-2xl border border-brand-border bg-white p-5 sm:p-7">
                <h2 class="text-lg font-bold text-slate-950">Membership</h2>
                <dl class="mt-5 space-y-5">
                    <div><dt class="text-xs font-bold uppercase tracking-wide text-slate-600">Number</dt><dd class="mt-1 font-mono text-sm font-semibold text-slate-900">{{ $member->membership_number }}</dd></div>
                    <div><dt class="text-xs font-bold uppercase tracking-wide text-slate-600">Category</dt><dd class="mt-1 text-sm font-semibold text-slate-900">{{ $member->membershipCategory?->name ?? $member->category }}</dd></div>
                    <div><dt class="text-xs font-bold uppercase tracking-wide text-slate-600">Level</dt><dd class="mt-1 text-sm font-semibold text-slate-900">{{ $member->membershipLevel?->name ?? $member->membership_level }}</dd></div>
                    <div><dt class="text-xs font-bold uppercase tracking-wide text-slate-600">Joined</dt><dd class="mt-1 text-sm font-semibold text-slate-900">{{ $member->joined_on->format('M j, Y') }}</dd></div>
                </dl>
            </section>

            <section class="cw-hover-surface rounded-2xl border border-brand-border bg-white p-5 sm:p-7">
                <h2 class="text-lg font-bold text-slate-950">Account status</h2>
                <p class="mt-2 text-sm font-medium leading-6 text-slate-600">{{ $member->is_active ? 'Deactivating keeps this member’s records available.' : 'Reactivate this member to mark their account active again.' }}</p>
                <form method="POST" action="{{ $member->is_active ? route('members.deactivate', $member) : route('members.reactivate', $member) }}" class="mt-5">
                    @csrf
                    @method('PATCH')
                    <button type="submit" onclick="return confirm('Are you sure you want to {{ $member->is_active ? 'deactivate' : 'reactivate' }} this member?')" class="rounded-lg border px-4 py-2.5 text-sm font-bold {{ $member->is_active ? 'border-amber-200 bg-amber-50 text-amber-800 hover:bg-amber-100' : 'border-emerald-200 bg-emerald-50 text-emerald-700 hover:bg-emerald-100' }}">{{ $member->is_active ? 'Deactivate member' : 'Reactivate member' }}</button>
                </form>
            </section>
        </div>
    </div>

    <a href="{{ route('members.history', $member) }}" class="mt-6 inline-flex rounded-xl border border-brand-border bg-brand-accent/10 px-4 py-3 text-sm font-bold text-brand-primary hover:bg-brand-accent/20">View transaction history</a>
@endsection

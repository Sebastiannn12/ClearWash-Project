@extends('layouts.app')

@section('title', 'Members')

@section('content')
    <div class="flex flex-wrap items-start justify-between gap-4">
        <div>
            <h1 class="text-3xl font-bold tracking-tight text-slate-950 sm:text-4xl">Members</h1>
            <p class="mt-3 text-sm font-medium leading-6 text-slate-600 sm:text-base">Manage member profiles and membership details in one place.</p>
        </div>
        <a href="{{ route('members.create') }}" class="inline-flex items-center justify-center rounded-xl bg-brand-primary px-4 py-2.5 text-sm font-semibold text-white transition-[background-color,transform] duration-150 hover:bg-brand-sidebar focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-accent active:scale-[0.98]">Add member</a>
    </div>

    @if (session('error'))
        <div role="alert" class="mt-6 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm font-medium text-red-800">{{ session('error') }}</div>
    @endif

    <div class="mt-8 grid gap-4 sm:grid-cols-3">
        <div class="rounded-2xl border border-brand-border bg-white p-5 sm:p-6"><p class="text-sm font-semibold text-brand-sidebar">Total members</p><p class="mt-5 text-3xl font-bold tracking-tight text-brand-sidebar tabular-nums">{{ number_format($totalMembers) }}</p></div>
        <div class="rounded-2xl border border-brand-border bg-white p-5 sm:p-6"><p class="text-sm font-semibold text-brand-sidebar">Active members</p><p class="mt-5 text-3xl font-bold tracking-tight text-brand-sidebar tabular-nums">{{ number_format($activeMembers) }}</p></div>
        <div class="rounded-2xl border border-brand-border bg-white p-5 sm:p-6"><p class="text-sm font-semibold text-brand-sidebar">Inactive members</p><p class="mt-5 text-3xl font-bold tracking-tight text-brand-sidebar tabular-nums">{{ number_format($inactiveMembers) }}</p></div>
    </div>

    <section class="mt-8 overflow-hidden rounded-2xl border border-brand-border bg-white" aria-labelledby="directory-heading">
        <div class="border-b border-brand-border p-5 sm:p-6">
            <div class="flex flex-wrap items-center justify-between gap-2">
                <div><h2 id="directory-heading" class="text-lg font-bold text-slate-950">Member directory</h2><p class="mt-1 text-sm font-medium text-slate-600">Search and filter your member records.</p></div>
                <span class="text-sm font-semibold text-slate-600">{{ $members->total() }} results</span>
            </div>
            <form action="{{ route('members.index') }}" method="GET" class="mt-5 grid gap-3 sm:grid-cols-2 2xl:grid-cols-[minmax(220px,1fr)_170px_170px_140px_auto]">
                <label class="sr-only" for="search">Search members</label>
                <input id="search" name="search" type="search" value="{{ request('search') }}" placeholder="Search name, number, category or level" class="w-full rounded-lg border border-brand-border bg-white px-3 py-2.5 text-sm font-medium outline-none focus:border-brand-primary focus:ring-2 focus:ring-brand-border">
                <label class="sr-only" for="membership_category_id">Category</label>
                <select id="membership_category_id" name="membership_category_id" class="rounded-lg border border-brand-border bg-white px-3 py-2.5 text-sm font-semibold focus:border-brand-primary focus:ring-2 focus:ring-brand-border"><option value="">All categories</option>@foreach ($categories as $category)<option value="{{ $category->id }}" @selected((string) request('membership_category_id') === (string) $category->id)>{{ $category->name }}</option>@endforeach</select>
                <label class="sr-only" for="membership_level_id">Membership level</label>
                <select id="membership_level_id" name="membership_level_id" class="rounded-lg border border-brand-border bg-white px-3 py-2.5 text-sm font-semibold focus:border-brand-primary focus:ring-2 focus:ring-brand-border"><option value="">All levels</option>@foreach ($levels as $level)<option value="{{ $level->id }}" @selected((string) request('membership_level_id') === (string) $level->id)>{{ $level->name }}</option>@endforeach</select>
                <label class="sr-only" for="status">Status</label>
                <select id="status" name="status" class="rounded-lg border border-brand-border bg-white px-3 py-2.5 text-sm font-semibold focus:border-brand-primary focus:ring-2 focus:ring-brand-border"><option value="">All statuses</option><option value="active" @selected(request('status') === 'active')>Active</option><option value="inactive" @selected(request('status') === 'inactive')>Inactive</option></select>
                <div class="flex gap-2"><button class="flex-1 rounded-lg bg-slate-900 px-4 py-2.5 text-sm font-semibold text-white transition-[background-color,transform] duration-150 hover:bg-slate-700 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-slate-700 active:scale-[0.98]">Search</button><a href="{{ route('members.index') }}" class="rounded-lg border border-brand-border px-4 py-2.5 text-sm font-medium text-slate-700 transition-[background-color,transform] duration-150 hover:bg-brand-canvas focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-accent active:scale-[0.98]">Clear</a></div>
            </form>
        </div>

        @if ($members->isEmpty())
            <div class="px-6 py-16 text-center"><div class="mx-auto flex size-12 items-center justify-center rounded-xl bg-brand-accent/10 text-brand-primary"><svg class="size-6" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M16 19a4 4 0 0 0-8 0m8 0H8m8 0h3a2 2 0 0 0 2-2v-1a4 4 0 0 0-3-3.87M8 19H5a2 2 0 0 1-2-2v-1a4 4 0 0 1 3-3.87M12 12a4 4 0 1 0 0-8 4 4 0 0 0 0 8Z"/></svg></div><h3 class="mt-4 font-semibold">{{ request()->hasAny(['search', 'membership_category_id', 'membership_level_id', 'status']) ? 'No matching members' : 'No members yet' }}</h3><p class="mt-2 text-sm text-slate-500">{{ request()->hasAny(['search', 'membership_category_id', 'membership_level_id', 'status']) ? 'Try different search terms or clear the filters.' : 'Register your first member to get started.' }}</p><a href="{{ request()->hasAny(['search', 'membership_category_id', 'membership_level_id', 'status']) ? route('members.index') : route('members.create') }}" class="mt-5 inline-flex rounded-lg bg-brand-primary px-4 py-2.5 text-sm font-semibold text-white">{{ request()->hasAny(['search', 'membership_category_id', 'membership_level_id', 'status']) ? 'Clear filters' : 'Add member' }}</a></div>
        @else
            <div class="divide-y divide-brand-border xl:hidden">
                @foreach ($members as $member)
                    <article class="p-5 sm:p-6">
                        <div class="flex flex-wrap items-start justify-between gap-3">
                            <div class="min-w-0">
                                <h3 class="font-semibold text-slate-900">{{ $member->full_name }}</h3>
                                <p class="mt-0.5 break-words text-xs text-slate-500">{{ $member->email ?: $member->phone }}</p>
                            </div>
                            <span class="inline-flex shrink-0 rounded-full px-2.5 py-1 text-xs font-semibold {{ $member->is_active ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-100 text-slate-600' }}">{{ $member->is_active ? 'Active' : 'Inactive' }}</span>
                        </div>
                        <dl class="mt-5 grid gap-4 text-sm sm:grid-cols-3">
                            <div><dt class="text-xs font-semibold uppercase tracking-wide text-slate-500">Membership no.</dt><dd class="mt-1 break-all font-mono text-xs text-slate-700">{{ $member->membership_number }}</dd></div>
                            <div><dt class="text-xs font-semibold uppercase tracking-wide text-slate-500">Category</dt><dd class="mt-1 font-medium text-slate-700">{{ $member->membershipCategory?->name ?? $member->category }}</dd></div>
                            <div><dt class="text-xs font-semibold uppercase tracking-wide text-slate-500">Level</dt><dd class="mt-1 font-medium text-slate-700">{{ $member->membershipLevel?->name ?? $member->membership_level }}</dd></div>
                        </dl>
                        <div class="mt-5 flex flex-wrap gap-2 border-t border-brand-border pt-4">
                            <a href="{{ route('members.show', $member) }}" class="rounded-lg border border-brand-border bg-slate-100 px-3 py-2 text-xs font-semibold text-slate-700 transition-colors hover:bg-slate-200">View</a>
                            <a href="{{ route('members.edit', $member) }}" class="rounded-lg border border-brand-border bg-brand-accent/10 px-3 py-2 text-xs font-semibold text-brand-primary transition-colors hover:bg-brand-accent/15">Edit</a>
                            @if ($member->is_active)
                                <form method="POST" action="{{ route('members.deactivate', $member) }}" onsubmit="return confirm('Deactivate this member? Their records and transaction history will remain available.')">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="rounded-lg border border-amber-200 bg-amber-50 px-3 py-2 text-xs font-semibold text-amber-800 transition-colors hover:bg-amber-100">Deactivate</button>
                                </form>
                            @else
                                <form method="POST" action="{{ route('members.reactivate', $member) }}">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="rounded-lg border border-emerald-200 bg-emerald-50 px-3 py-2 text-xs font-semibold text-emerald-700 transition-colors hover:bg-emerald-100">Reactivate</button>
                                </form>
                            @endif
                        </div>
                    </article>
                @endforeach
            </div>
            <div class="hidden xl:block">
                <table class="w-full text-left text-sm">
                    <thead class="border-y border-brand-border bg-brand-canvas text-xs font-bold uppercase tracking-[0.08em] text-brand-sidebar"><tr><th scope="col" class="px-6 py-3.5">Member</th><th scope="col" class="px-5 py-3.5">Membership no.</th><th scope="col" class="px-5 py-3.5">Category</th><th scope="col" class="px-5 py-3.5">Level</th><th scope="col" class="px-5 py-3.5">Status</th><th scope="col" class="px-6 py-3.5 text-right">Actions</th></tr></thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach ($members as $member)
                            <tr class="hover:bg-brand-canvas/70">
                                <td class="px-6 py-4"><div class="font-semibold text-slate-900">{{ $member->full_name }}</div><div class="mt-0.5 text-xs text-slate-500">{{ $member->email ?: $member->phone }}</div></td>
                                <td class="px-5 py-4 font-mono text-xs text-slate-600">{{ $member->membership_number }}</td>
                                <td class="px-5 py-4 text-slate-700">{{ $member->membershipCategory?->name ?? $member->category }}</td>
                                <td class="px-5 py-4 text-slate-700">{{ $member->membershipLevel?->name ?? $member->membership_level }}</td>
                                <td class="px-5 py-4"><span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ $member->is_active ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-100 text-slate-600' }}">{{ $member->is_active ? 'Active' : 'Inactive' }}</span></td>
                                <td class="px-6 py-4">
                                    <div class="flex flex-wrap items-center justify-end gap-2">
                                        <a href="{{ route('members.show', $member) }}" class="rounded-lg border border-brand-border bg-slate-100 px-3 py-2 text-xs font-semibold text-slate-700 transition-colors hover:bg-slate-200">View</a>
                                        <a href="{{ route('members.edit', $member) }}" class="rounded-lg border border-brand-border bg-brand-accent/10 px-3 py-2 text-xs font-semibold text-brand-primary transition-colors hover:bg-brand-accent/15">Edit</a>
                                        @if ($member->is_active)
                                            <form method="POST" action="{{ route('members.deactivate', $member) }}" onsubmit="return confirm('Deactivate this member? Their records and transaction history will remain available.')">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="rounded-lg border border-amber-200 bg-amber-50 px-3 py-2 text-xs font-semibold text-amber-800 transition-colors hover:bg-amber-100">Deactivate</button>
                                            </form>
                                        @else
                                            <form method="POST" action="{{ route('members.reactivate', $member) }}">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="rounded-lg border border-emerald-200 bg-emerald-50 px-3 py-2 text-xs font-semibold text-emerald-700 transition-colors hover:bg-emerald-100">Reactivate</button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="border-t border-brand-border px-5 py-4">{{ $members->links() }}</div>
        @endif
    </section>
@endsection

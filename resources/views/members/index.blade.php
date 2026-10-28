@extends('layouts.app')

@section('title', 'Members')

@section('content')
    <div class="flex flex-wrap items-start justify-between gap-4">
        <div>
            <p class="text-xs font-bold uppercase tracking-[0.15em] text-blue-800">Member management</p>
            <h1 class="mt-2 text-3xl font-bold tracking-tight text-slate-950">Members</h1>
            <p class="mt-2 text-sm font-medium text-slate-600">Manage member profiles and membership details in one place.</p>
        </div>
        <a href="{{ route('members.create') }}" class="inline-flex rounded-lg bg-[#1E3A8A] px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition-colors hover:bg-blue-900 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-700 sm:hidden">+ Add member</a>
    </div>

    <div class="mt-8 grid gap-4 sm:grid-cols-3">
        <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm"><p class="text-sm font-semibold text-slate-700">Total members</p><p class="mt-2 text-3xl font-bold tracking-tight text-slate-950">{{ number_format($totalMembers) }}</p></div>
        <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm"><p class="text-sm font-semibold text-slate-700">Active members</p><p class="mt-2 text-3xl font-bold tracking-tight text-blue-950">{{ number_format($activeMembers) }}</p></div>
        <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm"><p class="text-sm font-semibold text-slate-700">Inactive members</p><p class="mt-2 text-3xl font-bold tracking-tight text-slate-950">{{ number_format($inactiveMembers) }}</p></div>
    </div>

    <section class="mt-8 overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm" aria-labelledby="directory-heading">
        <div class="border-b border-slate-200 p-5 sm:p-6">
            <div class="flex flex-wrap items-center justify-between gap-2">
                <div><h2 id="directory-heading" class="text-lg font-bold text-slate-950">Member directory</h2><p class="mt-1 text-sm font-medium text-slate-600">Search and filter your member records.</p></div>
                <span class="text-sm font-semibold text-slate-600">{{ $members->total() }} results</span>
            </div>
            <form action="{{ route('members.index') }}" method="GET" class="mt-5 grid gap-3 sm:grid-cols-2 xl:grid-cols-[minmax(220px,1fr)_170px_170px_140px_auto]">
                <label class="sr-only" for="search">Search members</label>
                <input id="search" name="search" type="search" value="{{ request('search') }}" placeholder="Search name, number, category or level" class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm font-medium outline-none focus:border-blue-700 focus:ring-2 focus:ring-blue-100">
                <label class="sr-only" for="category">Category</label>
                <select id="category" name="category" class="rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm font-semibold focus:border-blue-700 focus:ring-2 focus:ring-blue-100"><option value="">All categories</option>@foreach (['Individual', 'Corporate'] as $category)<option value="{{ $category }}" @selected(request('category') === $category)>{{ $category }}</option>@endforeach</select>
                <label class="sr-only" for="membership_level">Membership level</label>
                <select id="membership_level" name="membership_level" class="rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm font-semibold focus:border-blue-700 focus:ring-2 focus:ring-blue-100"><option value="">All levels</option>@foreach (['Basic', 'Plus', 'Premium'] as $level)<option value="{{ $level }}" @selected(request('membership_level') === $level)>{{ $level }}</option>@endforeach</select>
                <label class="sr-only" for="status">Status</label>
                <select id="status" name="status" class="rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm font-semibold focus:border-blue-700 focus:ring-2 focus:ring-blue-100"><option value="">All statuses</option><option value="active" @selected(request('status') === 'active')>Active</option><option value="inactive" @selected(request('status') === 'inactive')>Inactive</option></select>
                <div class="flex gap-2"><button class="flex-1 rounded-lg bg-slate-900 px-4 py-2.5 text-sm font-semibold text-white hover:bg-slate-700">Search</button><a href="{{ route('members.index') }}" class="rounded-lg border border-slate-300 px-4 py-2.5 text-sm font-medium text-slate-700 hover:bg-slate-50">Clear</a></div>
            </form>
        </div>

        @if ($members->isEmpty())
            <div class="px-6 py-16 text-center"><div class="mx-auto flex size-12 items-center justify-center rounded-xl bg-slate-100 text-xl text-blue-900">◎</div><h3 class="mt-4 font-semibold">{{ request()->hasAny(['search', 'category', 'membership_level', 'status']) ? 'No matching members' : 'No members yet' }}</h3><p class="mt-2 text-sm text-slate-500">{{ request()->hasAny(['search', 'category', 'membership_level', 'status']) ? 'Try different search terms or clear the filters.' : 'Register your first member to get started.' }}</p><a href="{{ request()->hasAny(['search', 'category', 'membership_level', 'status']) ? route('members.index') : route('members.create') }}" class="mt-5 inline-flex rounded-lg bg-[#1E3A8A] px-4 py-2.5 text-sm font-semibold text-white">{{ request()->hasAny(['search', 'category', 'membership_level', 'status']) ? 'Clear filters' : 'Add member' }}</a></div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full min-w-[1060px] text-left text-sm">
                    <thead class="border-y border-blue-100 bg-blue-50/70 text-xs font-bold uppercase tracking-[0.08em] text-blue-950"><tr><th scope="col" class="px-6 py-3.5">Member</th><th scope="col" class="px-5 py-3.5">Membership no.</th><th scope="col" class="px-5 py-3.5">Category</th><th scope="col" class="px-5 py-3.5">Level</th><th scope="col" class="px-5 py-3.5">Status</th><th scope="col" class="px-6 py-3.5 text-right">Actions</th></tr></thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach ($members as $member)
                            <tr class="hover:bg-slate-50/70">
                                <td class="px-6 py-4"><div class="font-semibold text-slate-900">{{ $member->full_name }}</div><div class="mt-0.5 text-xs text-slate-500">{{ $member->email ?: $member->phone }}</div></td>
                                <td class="px-5 py-4 font-mono text-xs text-slate-600">{{ $member->membership_number }}</td>
                                <td class="px-5 py-4 text-slate-700">{{ $member->category }}</td>
                                <td class="px-5 py-4 text-slate-700">{{ $member->membership_level }}</td>
                                <td class="px-5 py-4"><span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ $member->is_active ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-100 text-slate-600' }}">{{ $member->is_active ? 'Active' : 'Inactive' }}</span></td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="{{ route('members.show', $member) }}" class="rounded-lg border border-slate-200 px-3 py-2 text-xs font-semibold text-slate-700 transition-colors hover:border-slate-300 hover:bg-slate-50">View</a>
                                        <a href="{{ route('members.edit', $member) }}" class="rounded-lg border border-blue-200 px-3 py-2 text-xs font-semibold text-blue-800 transition-colors hover:bg-blue-50">Edit</a>
                                        @unless ($member->is_active)
                                            <form method="POST" action="{{ route('members.reactivate', $member) }}">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="rounded-lg border border-emerald-200 px-3 py-2 text-xs font-semibold text-emerald-700 transition-colors hover:bg-emerald-50">Reactivate</button>
                                            </form>
                                        @endunless
                                        <form method="POST" action="{{ route('members.force-delete', $member) }}">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" onclick="return confirm('Permanently delete this member? This action cannot be undone.')" class="rounded-lg border border-red-200 px-3 py-2 text-xs font-semibold text-red-700 transition-colors hover:bg-red-50">Delete</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="border-t border-slate-200 px-5 py-4">{{ $members->links() }}</div>
        @endif
    </section>
@endsection

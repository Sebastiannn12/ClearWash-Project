@extends('layouts.app')
@section('title', 'Edit member')
@section('content')
    <a href="{{ route('members.index') }}" class="inline-flex items-center rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 transition-colors hover:bg-slate-50">← Back to members</a>
    <div class="mt-5 flex flex-wrap items-start justify-between gap-4">
        <div><p class="text-xs font-bold uppercase tracking-[0.15em] text-blue-800">Member management</p><h1 class="mt-2 text-3xl font-bold tracking-tight text-slate-950">Edit member</h1><p class="mt-2 text-sm font-medium text-slate-600">Update {{ $member->full_name }}’s profile. Membership number {{ $member->membership_number }} stays the same.</p></div>
        <a href="{{ route('members.create') }}" class="inline-flex items-center rounded-lg bg-[#1E3A8A] px-4 py-2.5 text-sm font-bold text-white shadow-sm transition-colors hover:bg-blue-900">+ Add Member</a>
    </div>
    <form method="POST" action="{{ route('members.update', $member) }}" class="mt-8 max-w-4xl rounded-xl border border-slate-200 bg-white p-5 shadow-sm [&_input]:font-medium [&_label]:font-bold [&_select]:font-semibold [&_textarea]:font-medium sm:p-8">
        @csrf @method('PUT')
        <div class="mb-7 border-b border-slate-100 pb-5"><h2 class="text-lg font-bold text-slate-950">Member details</h2><p class="mt-1 text-sm font-medium text-slate-600">Fields marked * are required.</p></div>
        @include('members._form')
        <div class="mt-8 flex flex-wrap gap-3 border-t border-slate-100 pt-6">
            <button class="rounded-lg bg-[#1E3A8A] px-5 py-2.5 text-sm font-bold text-white shadow-sm hover:bg-blue-900">Save changes</button>
            <a href="{{ route('members.show', $member) }}" class="rounded-lg border border-slate-300 bg-white px-5 py-2.5 text-sm font-semibold text-slate-800 hover:bg-slate-50">Cancel</a>
            <button type="button" onclick="document.getElementById('soft-delete-{{ $member->id }}').showModal()" class="rounded-lg border border-red-200 bg-red-50 px-5 py-2.5 text-sm font-bold text-red-700 transition-colors hover:bg-red-100">Delete member</button>
        </div>
    </form>
    @include('members._delete-modal', ['member' => $member, 'permanent' => false])
@endsection

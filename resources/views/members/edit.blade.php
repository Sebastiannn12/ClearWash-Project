@extends('layouts.app')
@section('title', 'Edit member')
@section('content')
    <a href="{{ route('members.show', $member) }}" class="text-sm font-medium text-blue-800 hover:underline">← Back to profile</a>
    <div class="mt-5"><p class="text-xs font-semibold uppercase tracking-[0.15em] text-blue-800">Member management</p><h1 class="mt-2 text-3xl font-semibold tracking-tight">Edit member</h1><p class="mt-2 text-sm text-slate-500">Update {{ $member->full_name }}’s profile. Membership number {{ $member->membership_number }} stays the same.</p></div>
    <form method="POST" action="{{ route('members.update', $member) }}" class="mt-8 max-w-4xl rounded-xl border border-slate-200 bg-white p-5 shadow-sm sm:p-8">
        @csrf @method('PUT')
        <div class="mb-7 border-b border-slate-100 pb-5"><h2 class="text-lg font-semibold">Member details</h2><p class="mt-1 text-sm text-slate-500">Fields marked * are required.</p></div>
        @include('members._form')
        <div class="mt-8 flex flex-wrap gap-3 border-t border-slate-100 pt-6"><button class="rounded-lg bg-[#1E3A8A] px-5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-blue-900">Save changes</button><a href="{{ route('members.show', $member) }}" class="rounded-lg border border-slate-300 px-5 py-2.5 text-sm font-medium text-slate-700 hover:bg-slate-50">Cancel</a></div>
    </form>
@endsection

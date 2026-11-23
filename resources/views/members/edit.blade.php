@extends('layouts.app')
@section('title', 'Edit member')
@section('content')
    <a href="{{ route('members.index') }}" class="inline-flex items-center gap-2 text-sm font-semibold text-brand-primary underline-offset-4 hover:underline focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-brand-accent"><svg class="size-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="m15 18-6-6 6-6"/></svg>Back to members</a>
    <div class="mt-5 flex flex-wrap items-start justify-between gap-4">
        <div><h1 class="text-3xl font-bold tracking-tight text-slate-950 sm:text-4xl">Edit member</h1><p class="mt-3 text-sm font-medium leading-6 text-slate-600 sm:text-base">Update {{ $member->full_name }}’s profile. Membership number {{ $member->membership_number }} stays the same.</p></div>
        <a href="{{ route('members.create') }}" class="inline-flex items-center rounded-xl bg-brand-primary px-4 py-2.5 text-sm font-bold text-white transition-[background-color,transform] duration-150 hover:bg-brand-sidebar active:scale-[0.98]">Add member</a>
    </div>
    <form method="POST" action="{{ route('members.update', $member) }}" class="mt-8 max-w-4xl rounded-2xl border border-brand-border bg-white p-5 [&_input]:font-medium [&_label]:font-bold [&_select]:font-semibold [&_textarea]:font-medium sm:p-8">
        @csrf @method('PUT')
        <div class="mb-7 border-b border-brand-border/70 pb-5"><h2 class="text-lg font-bold text-slate-950">Member details</h2><p class="mt-1 text-sm font-medium text-slate-600">Fields marked * are required.</p></div>
        @include('members._form')
        <div class="mt-8 flex flex-col-reverse gap-3 border-t border-brand-border/70 pt-6 sm:flex-row sm:flex-wrap">
            <button type="button" onclick="document.getElementById('permanent-delete-{{ $member->id }}').showModal()" class="rounded-xl border border-red-200 bg-red-50 px-5 py-2.5 text-sm font-bold text-red-700 transition-[background-color,transform] duration-150 hover:bg-red-100 active:scale-[0.98]">Delete member</button>
            <a href="{{ route('members.show', $member) }}" class="rounded-xl border border-brand-border bg-white px-5 py-2.5 text-center text-sm font-semibold text-slate-800 transition-[background-color,transform] duration-150 hover:bg-brand-canvas active:scale-[0.98]">Cancel</a>
            <button class="rounded-xl bg-brand-primary px-5 py-2.5 text-sm font-bold text-white transition-[background-color,transform] duration-150 hover:bg-brand-sidebar focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-accent active:scale-[0.98]">Save changes</button>
        </div>
    </form>
    @include('members._delete-modal', ['member' => $member])
@endsection

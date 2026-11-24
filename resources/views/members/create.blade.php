@extends('layouts.app')
@section('title', 'Add member')
@section('content')
    <a href="{{ route('members.index') }}" class="inline-flex items-center gap-2 text-sm font-semibold text-brand-primary underline-offset-4 hover:underline focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-brand-accent"><svg class="size-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="m15 18-6-6 6-6"/></svg>Back to members</a>
    <div class="mt-6"><h1 class="text-3xl font-bold tracking-tight text-slate-950 sm:text-4xl">Register a member</h1><p class="mt-3 text-sm font-medium leading-6 text-slate-600 sm:text-base">Enter their details below. A unique membership number is assigned automatically.</p></div>
    <form method="POST" action="{{ route('members.store') }}" data-price-preview class="mt-8 max-w-4xl rounded-2xl border border-brand-border bg-white p-5 [&_input]:font-medium [&_label]:font-bold [&_select]:font-semibold [&_textarea]:font-medium sm:p-8">
        @csrf
        <div class="mb-7 border-b border-brand-border/70 pb-5"><h2 class="text-lg font-bold text-slate-950">Member details</h2><p class="mt-1 text-sm font-medium text-slate-600">Fields marked with a dot are required.</p></div>
        @include('members._form')
        @include('members._location-picker', ['saveCoordinates' => true])
        <div class="mt-6">@include('members._pricing-preview')</div>
        <div class="mt-8 flex flex-col-reverse gap-3 border-t border-brand-border/70 pt-6 sm:flex-row"><a href="{{ route('members.index') }}" class="rounded-xl border border-brand-border px-5 py-2.5 text-center text-sm font-semibold text-slate-800 transition-[background-color,transform] duration-150 hover:bg-brand-canvas active:scale-[0.98]">Cancel</a><button class="rounded-xl bg-brand-primary px-5 py-2.5 text-sm font-bold text-white transition-[background-color,transform] duration-150 hover:bg-brand-sidebar focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-accent active:scale-[0.98]">Register member</button></div>
    </form>
@endsection

@extends('layouts.app')
@section('title', 'Add member')
@section('content')
    <a href="{{ route('members.index') }}" class="text-sm font-semibold text-blue-900 hover:underline">← Back to members</a>
    <div class="mt-5"><p class="text-xs font-bold uppercase tracking-[0.15em] text-blue-800">Member management</p><h1 class="mt-2 text-3xl font-bold tracking-tight text-slate-950">Register a member</h1><p class="mt-2 text-sm font-medium text-slate-600">Enter their details below. A unique membership number is assigned automatically.</p></div>
    <form method="POST" action="{{ route('members.store') }}" class="mt-8 max-w-4xl rounded-xl border border-slate-200 bg-white p-5 shadow-sm [&_input]:font-medium [&_label]:font-bold [&_select]:font-semibold [&_textarea]:font-medium sm:p-8">
        @csrf
        <div class="mb-7 border-b border-slate-100 pb-5"><h2 class="text-lg font-bold text-slate-950">Member details</h2><p class="mt-1 text-sm font-medium text-slate-600">Fields marked * are required.</p></div>
        @include('members._form')
        <div class="mt-8 flex flex-wrap gap-3 border-t border-slate-100 pt-6"><button class="rounded-lg bg-[#1E3A8A] px-5 py-2.5 text-sm font-bold text-white shadow-sm hover:bg-blue-900">Register member</button><a href="{{ route('members.index') }}" class="rounded-lg border border-slate-300 px-5 py-2.5 text-sm font-semibold text-slate-800 hover:bg-slate-50">Cancel</a></div>
    </form>
@endsection

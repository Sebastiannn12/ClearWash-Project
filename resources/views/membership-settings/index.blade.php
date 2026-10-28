@extends('layouts.app')

@section('title', 'Membership setup')

@section('content')
    <div>
        <p class="text-xs font-bold uppercase tracking-[0.15em] text-blue-800">Membership settings</p>
        <h1 class="mt-2 text-3xl font-bold tracking-tight text-slate-950">Categories and levels</h1>
        <p class="mt-2 max-w-2xl text-sm font-medium leading-6 text-slate-600">Review the available member categories and configure the fee, validity period, and benefits for each membership level.</p>
    </div>

    <section class="mt-8 rounded-xl border border-slate-200 bg-white p-5 shadow-sm sm:p-7" aria-labelledby="categories-heading">
        <div>
            <h2 id="categories-heading" class="text-lg font-bold text-slate-950">Member categories</h2>
            <p class="mt-1 text-sm font-medium text-slate-600">These categories are available when registering or editing a member.</p>
        </div>
        <div class="mt-5 grid gap-4 sm:grid-cols-2">
            @foreach ($categories as $category)
                <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                    <div class="flex items-center gap-3">
                        <span class="flex size-9 shrink-0 items-center justify-center rounded-lg bg-blue-100 text-sm font-bold text-blue-900">{{ substr($category->name, 0, 1) }}</span>
                        <span class="font-bold text-slate-950">{{ $category->name }}</span>
                    </div>
                    <p class="mt-3 text-sm font-medium leading-6 text-slate-600">{{ $category->benefits }}</p>
                </div>
            @endforeach
        </div>
    </section>

    <section class="mt-6" aria-labelledby="levels-heading">
        <div>
            <h2 id="levels-heading" class="text-lg font-bold text-slate-950">Membership levels</h2>
            <p class="mt-1 text-sm font-medium text-slate-600">Leave a field blank when that detail does not apply to a level.</p>
        </div>

        <div class="mt-5 grid gap-5 xl:grid-cols-2">
            @foreach ($levels as $level)
                <form method="POST" action="{{ route('membership-settings.levels.update', $level) }}" class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
                    @csrf
                    @method('PATCH')

                    <div class="flex items-center justify-between gap-3 border-b border-slate-100 pb-4">
                        <div>
                            <p class="text-xs font-bold uppercase tracking-[0.12em] text-blue-800">Membership level</p>
                            <h3 class="mt-1 text-xl font-bold text-slate-950">{{ $level->name }}</h3>
                        </div>
                        <span class="rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-bold text-emerald-800">Active</span>
                    </div>

                    <div class="mt-5 grid gap-5 sm:grid-cols-2">
                        <div>
                            <label for="fee-{{ $level->id }}" class="text-sm font-bold text-slate-800">Fee</label>
                            <div class="relative mt-1.5">
                                <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 text-sm font-semibold text-slate-500">₱</span>
                                <input id="fee-{{ $level->id }}" name="fee" type="number" min="0" max="99999999.99" step="0.01" value="{{ old('fee', $level->fee) }}" placeholder="0.00" class="w-full rounded-lg border border-slate-300 bg-white py-2.5 pr-3.5 pl-8 text-sm font-semibold text-slate-900 outline-none focus:border-blue-700 focus:ring-2 focus:ring-blue-100">
                            </div>
                            @error('fee')<p class="mt-1 text-xs font-medium text-red-700">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label for="validity-{{ $level->id }}" class="text-sm font-bold text-slate-800">Validity period</label>
                            <div class="relative mt-1.5">
                                <input id="validity-{{ $level->id }}" name="validity_days" type="number" min="1" max="3650" value="{{ old('validity_days', $level->validity_days) }}" placeholder="30" class="w-full rounded-lg border border-slate-300 bg-white py-2.5 pr-14 pl-3.5 text-sm font-semibold text-slate-900 outline-none focus:border-blue-700 focus:ring-2 focus:ring-blue-100">
                                <span class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3.5 text-xs font-semibold text-slate-500">days</span>
                            </div>
                            @error('validity_days')<p class="mt-1 text-xs font-medium text-red-700">{{ $message }}</p>@enderror
                        </div>
                        <div class="sm:col-span-2">
                            <label for="benefits-{{ $level->id }}" class="text-sm font-bold text-slate-800">Benefits</label>
                            <textarea id="benefits-{{ $level->id }}" name="benefits" rows="3" maxlength="2000" placeholder="Describe the benefits included in this level" class="mt-1.5 w-full rounded-lg border border-slate-300 bg-white px-3.5 py-2.5 text-sm font-medium text-slate-900 outline-none placeholder:text-slate-400 focus:border-blue-700 focus:ring-2 focus:ring-blue-100">{{ old('benefits', $level->benefits) }}</textarea>
                            @error('benefits')<p class="mt-1 text-xs font-medium text-red-700">{{ $message }}</p>@enderror
                        </div>
                    </div>

                    <div class="mt-5 flex justify-end border-t border-slate-100 pt-4">
                        <button type="submit" class="rounded-lg bg-[#1E3A8A] px-4 py-2.5 text-sm font-bold text-white shadow-sm transition-colors hover:bg-blue-900">Save {{ $level->name }}</button>
                    </div>
                </form>
            @endforeach
        </div>
    </section>
@endsection

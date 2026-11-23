@extends('layouts.app')

@section('title', $page === 'categories' ? 'Membership Categories' : 'Membership Levels')

@section('content')
    <div>
        <h1 class="text-3xl font-bold tracking-tight text-slate-950 sm:text-4xl">{{ $page === 'categories' ? 'Membership Categories' : 'Membership Levels' }}</h1>
        <p class="mt-3 max-w-2xl text-sm font-medium leading-6 text-slate-600 sm:text-base">{{ $page === 'categories' ? 'Manage member categories, benefits, and discounts.' : 'Manage pricing, validity periods, and membership benefits.' }}</p>
    </div>

    @if (session('error'))
        <div role="alert" class="mt-6 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm font-medium text-red-800">{{ session('error') }}</div>
    @endif

    @if ($page === 'categories')
    @php($categoryCreateBag = $errors->getBag('category-create'))
    <section class="mt-8 rounded-2xl border border-brand-border bg-white p-5 sm:p-7" aria-labelledby="categories-heading">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
            <div>
                <h2 id="categories-heading" class="text-2xl font-bold text-slate-950">Member categories</h2>
                <p class="mt-1 text-sm font-medium text-slate-600">The four required categories are protected. Additional categories can be created and managed here.</p>
            </div>
            <button type="button" onclick="document.getElementById('add-category-modal').showModal()" class="shrink-0 rounded-xl bg-brand-primary px-4 py-2.5 text-sm font-bold text-white transition-[background-color,transform] duration-150 hover:bg-brand-sidebar active:scale-[0.98]">Add category</button>
        </div>

        <dialog id="add-category-modal" aria-labelledby="add-category-title" class="fixed inset-0 m-auto max-h-[calc(100vh-2rem)] w-[calc(100vw-2rem)] max-w-lg overflow-y-auto rounded-2xl border-0 bg-white p-0 text-left text-slate-900 shadow-2xl backdrop:bg-slate-950/60">
        <form method="POST" action="{{ route('membership-settings.categories.store') }}" class="p-5 sm:p-7">
            @csrf
            <input type="hidden" name="is_active" value="0">
            <div class="flex items-start justify-between gap-4"><h3 id="add-category-title" class="text-xl font-bold text-slate-950">Add category</h3><button type="button" onclick="document.getElementById('add-category-modal').close()" aria-label="Close" class="flex size-9 items-center justify-center rounded-lg text-slate-500 transition-colors hover:bg-slate-100 hover:text-slate-900 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-accent"><svg class="size-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" d="m6 6 12 12M18 6 6 18"/></svg></button></div>
            <div class="mt-5 grid gap-4">
                <div>
                    <label for="new-category-name" class="text-sm font-bold text-slate-800">Category name</label>
                    <input id="new-category-name" name="name" value="{{ $categoryCreateBag->any() ? old('name') : '' }}" required maxlength="255" class="mt-1.5 w-full rounded-lg border border-brand-border bg-white px-3.5 py-2.5 text-sm font-semibold outline-none focus:border-brand-primary focus:ring-2 focus:ring-brand-border">
                    @error('name', 'category-create')<p class="mt-1 text-xs font-medium text-red-700">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="new-category-benefits" class="text-sm font-bold text-slate-800">Benefits</label>
                    <textarea id="new-category-benefits" name="benefits" rows="2" maxlength="2000" class="mt-1.5 w-full rounded-lg border border-brand-border bg-white px-3.5 py-2.5 text-sm font-medium outline-none focus:border-brand-primary focus:ring-2 focus:ring-brand-border">{{ $categoryCreateBag->any() ? old('benefits') : '' }}</textarea>
                    @error('benefits', 'category-create')<p class="mt-1 text-xs font-medium text-red-700">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="new-category-discount" class="text-sm font-bold text-slate-800">Discount percentage</label>
                    <input id="new-category-discount" name="discount_percentage" type="number" min="0" max="100" step="0.01" value="{{ $categoryCreateBag->any() ? old('discount_percentage') : '0' }}" required class="mt-1.5 w-full rounded-lg border border-brand-border bg-white px-3.5 py-2.5 text-sm font-semibold outline-none focus:border-brand-primary focus:ring-2 focus:ring-brand-border">
                    @error('discount_percentage', 'category-create')<p class="mt-1 text-xs font-medium text-red-700">{{ $message }}</p>@enderror
                </div>
                <div class="flex flex-col gap-3">
                    <label class="inline-flex items-center gap-2 text-sm font-semibold text-slate-700">
                        <input name="is_active" type="checkbox" value="1" @checked(! $categoryCreateBag->any() || old('is_active')) class="size-4 rounded border-brand-border text-brand-primary focus:ring-brand-accent">
                        Active
                    </label>
                    @error('is_active', 'category-create')<p class="text-xs font-medium text-red-700">{{ $message }}</p>@enderror
                </div>
            </div>
            <div class="mt-6 flex flex-col-reverse gap-3 border-t border-brand-border/70 pt-5 sm:flex-row sm:justify-end"><button type="button" onclick="document.getElementById('add-category-modal').close()" class="rounded-xl border border-brand-border px-4 py-2.5 text-sm font-semibold text-slate-700 transition-[background-color,transform] duration-150 hover:bg-brand-canvas active:scale-[0.98]">Cancel</button><button type="submit" class="rounded-xl bg-brand-primary px-4 py-2.5 text-sm font-bold text-white transition-[background-color,transform] duration-150 hover:bg-brand-sidebar active:scale-[0.98]">Add category</button></div>
        </form>
        </dialog>

        <div class="mt-5 grid gap-5 xl:grid-cols-2">
            @foreach ($categories as $category)
                @php($categoryBag = $errors->getBag('category-'.$category->id))
                <form method="POST" action="{{ route('membership-settings.categories.update', $category) }}" class="rounded-2xl border border-brand-border bg-brand-canvas/40 p-5">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="is_active" value="{{ $category->isRequired() ? 1 : 0 }}">

                    <div class="flex items-center justify-between gap-3 border-b border-brand-border/70 pb-4">
                        <span class="text-xs font-bold uppercase tracking-[0.12em] text-brand-primary">Member category</span>
                        <div class="flex items-center gap-2">
                            @if ($category->isRequired())
                                <span class="rounded-full bg-brand-accent/10 px-2.5 py-1 text-xs font-bold text-brand-primary">Required</span>
                            @endif
                            <span class="rounded-full px-2.5 py-1 text-xs font-bold {{ $category->is_active ? 'bg-emerald-50 text-emerald-800' : 'bg-slate-100 text-slate-600' }}">{{ $category->is_active ? 'Active' : 'Inactive' }}</span>
                        </div>
                    </div>

                    <div class="mt-5 space-y-4">
                        <div>
                            <label for="category-name-{{ $category->id }}" class="text-sm font-bold text-slate-800">Name</label>
                            <input id="category-name-{{ $category->id }}" name="name" value="{{ $categoryBag->any() ? old('name') : $category->name }}" required maxlength="255" @readonly($category->isRequired()) class="mt-1.5 w-full rounded-lg border border-brand-border px-3.5 py-2.5 text-sm font-semibold outline-none focus:border-brand-primary focus:ring-2 focus:ring-brand-border {{ $category->isRequired() ? 'bg-slate-100 text-slate-600' : 'bg-white text-slate-900' }}">
                            @error('name', 'category-'.$category->id)<p class="mt-1 text-xs font-medium text-red-700">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label for="category-benefits-{{ $category->id }}" class="text-sm font-bold text-slate-800">Benefits</label>
                            <textarea id="category-benefits-{{ $category->id }}" name="benefits" rows="3" maxlength="2000" class="mt-1.5 w-full rounded-lg border border-brand-border bg-white px-3.5 py-2.5 text-sm font-medium outline-none focus:border-brand-primary focus:ring-2 focus:ring-brand-border">{{ $categoryBag->any() ? old('benefits') : $category->benefits }}</textarea>
                            @error('benefits', 'category-'.$category->id)<p class="mt-1 text-xs font-medium text-red-700">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label for="category-discount-{{ $category->id }}" class="text-sm font-bold text-slate-800">Discount percentage</label>
                            <input id="category-discount-{{ $category->id }}" name="discount_percentage" type="number" min="0" max="100" step="0.01" value="{{ $categoryBag->any() ? old('discount_percentage') : $category->discount_percentage }}" required class="mt-1.5 w-full rounded-lg border border-brand-border bg-white px-3.5 py-2.5 text-sm font-semibold outline-none focus:border-brand-primary focus:ring-2 focus:ring-brand-border">
                            @error('discount_percentage', 'category-'.$category->id)<p class="mt-1 text-xs font-medium text-red-700">{{ $message }}</p>@enderror
                        </div>
                        @unless ($category->isRequired())
                            <label class="inline-flex items-center gap-2 text-sm font-semibold text-slate-700">
                                <input name="is_active" type="checkbox" value="1" @checked($categoryBag->any() ? old('is_active') : $category->is_active) class="size-4 rounded border-brand-border text-brand-primary focus:ring-brand-accent">
                                Active and available for member assignment
                            </label>
                        @endunless
                        @error('is_active', 'category-'.$category->id)<p class="text-xs font-medium text-red-700">{{ $message }}</p>@enderror
                    </div>

                    <div class="mt-5 flex flex-wrap justify-end gap-2 border-t border-brand-border/70 pt-4">
                        @unless ($category->isRequired())
                            <button type="button" onclick="document.getElementById('delete-category-{{ $category->id }}').showModal()" class="rounded-lg border border-red-200 bg-red-50 px-4 py-2.5 text-sm font-semibold text-red-700 hover:bg-red-100">Delete</button>
                        @endunless
                        <button type="submit" class="rounded-xl bg-brand-primary px-4 py-2.5 text-sm font-bold text-white transition-[background-color,transform] duration-150 hover:bg-brand-sidebar active:scale-[0.98]">Save category</button>
                    </div>
                </form>
            @endforeach
        </div>
    </section>

    @else
    @php($levelCreateBag = $errors->getBag('level-create'))
    <section class="mt-10 rounded-2xl border border-brand-border bg-white p-5 sm:p-7" aria-labelledby="levels-heading">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
            <div>
                <h2 id="levels-heading" class="text-2xl font-bold text-slate-950">Membership levels</h2>
                <p class="mt-2 max-w-2xl text-sm font-medium leading-6 text-slate-700">Active levels require a fee and validity period because these values are used by TPS transactions.</p>
            </div>
            <button type="button" onclick="document.getElementById('add-level-modal').showModal()" class="shrink-0 rounded-xl bg-brand-primary px-4 py-2.5 text-sm font-bold text-white transition-[background-color,transform] duration-150 hover:bg-brand-sidebar active:scale-[0.98]">Add level</button>
        </div>

        <dialog id="add-level-modal" aria-labelledby="add-level-title" class="fixed inset-0 m-auto max-h-[calc(100vh-2rem)] w-[calc(100vw-2rem)] max-w-lg overflow-y-auto rounded-2xl border-0 bg-white p-0 text-left text-slate-900 shadow-2xl backdrop:bg-slate-950/60">
        <form method="POST" action="{{ route('membership-settings.levels.store') }}" class="p-5 sm:p-7">
            @csrf
            <input type="hidden" name="is_active" value="0">
            <div class="flex items-start justify-between gap-4"><h3 id="add-level-title" class="text-xl font-bold text-slate-950">Add level</h3><button type="button" onclick="document.getElementById('add-level-modal').close()" aria-label="Close" class="flex size-9 items-center justify-center rounded-lg text-slate-500 transition-colors hover:bg-slate-100 hover:text-slate-900 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-accent"><svg class="size-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" d="m6 6 12 12M18 6 6 18"/></svg></button></div>
            <div class="mt-5 grid gap-5 sm:grid-cols-2">
                <div>
                    <label for="new-level-name" class="text-sm font-bold text-slate-800">Level name</label>
                    <input id="new-level-name" name="name" value="{{ $levelCreateBag->any() ? old('name') : '' }}" required maxlength="255" class="mt-1.5 w-full rounded-lg border border-brand-border bg-white px-3.5 py-2.5 text-sm font-semibold outline-none focus:border-brand-primary focus:ring-2 focus:ring-brand-border">
                    @error('name', 'level-create')<p class="mt-1 text-xs font-medium text-red-700">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="new-level-fee" class="text-sm font-bold text-slate-800">Fee</label>
                    <input id="new-level-fee" name="fee" type="number" min="0" max="99999999.99" step="0.01" value="{{ $levelCreateBag->any() ? old('fee') : '' }}" class="mt-1.5 w-full rounded-lg border border-brand-border bg-white px-3.5 py-2.5 text-sm font-semibold outline-none focus:border-brand-primary focus:ring-2 focus:ring-brand-border">
                    @error('fee', 'level-create')<p class="mt-1 text-xs font-medium text-red-700">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="new-level-validity" class="text-sm font-bold text-slate-800">Validity days</label>
                    <input id="new-level-validity" name="validity_days" type="number" min="1" max="3650" value="{{ $levelCreateBag->any() ? old('validity_days') : '' }}" class="mt-1.5 w-full rounded-lg border border-brand-border bg-white px-3.5 py-2.5 text-sm font-semibold outline-none focus:border-brand-primary focus:ring-2 focus:ring-brand-border">
                    @error('validity_days', 'level-create')<p class="mt-1 text-xs font-medium text-red-700">{{ $message }}</p>@enderror
                </div>
                <div class="flex flex-col gap-3 sm:pt-7">
                    <label class="inline-flex items-center gap-2 text-sm font-semibold text-slate-700">
                        <input name="is_active" type="checkbox" value="1" @checked(! $levelCreateBag->any() || old('is_active')) class="size-4 rounded border-brand-border text-brand-primary focus:ring-brand-accent">
                        Active
                    </label>
                    @error('is_active', 'level-create')<p class="text-xs font-medium text-red-700">{{ $message }}</p>@enderror
                </div>
                <div class="sm:col-span-2">
                    <label for="new-level-benefits" class="text-sm font-bold text-slate-800">Benefits</label>
                    <textarea id="new-level-benefits" name="benefits" rows="2" maxlength="2000" class="mt-1.5 w-full rounded-lg border border-brand-border bg-white px-3.5 py-2.5 text-sm font-medium outline-none focus:border-brand-primary focus:ring-2 focus:ring-brand-border">{{ $levelCreateBag->any() ? old('benefits') : '' }}</textarea>
                    @error('benefits', 'level-create')<p class="mt-1 text-xs font-medium text-red-700">{{ $message }}</p>@enderror
                </div>
            </div>
            <div class="mt-6 flex flex-col-reverse gap-3 border-t border-brand-border/70 pt-5 sm:flex-row sm:justify-end"><button type="button" onclick="document.getElementById('add-level-modal').close()" class="rounded-xl border border-brand-border px-4 py-2.5 text-sm font-semibold text-slate-700 transition-[background-color,transform] duration-150 hover:bg-brand-canvas active:scale-[0.98]">Cancel</button><button type="submit" class="rounded-xl bg-brand-primary px-4 py-2.5 text-sm font-bold text-white transition-[background-color,transform] duration-150 hover:bg-brand-sidebar active:scale-[0.98]">Add level</button></div>
        </form>
        </dialog>

        <div class="mt-7 grid gap-6 xl:grid-cols-2">
            @foreach ($levels as $level)
                @php($levelBag = $errors->getBag('level-'.$level->id))
                <form method="POST" action="{{ route('membership-settings.levels.update', $level) }}" class="rounded-2xl border border-brand-border bg-brand-canvas/40 p-5 sm:p-6">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="is_active" value="{{ $level->isRequired() ? 1 : 0 }}">

                    <div class="flex items-center justify-between gap-3 border-b border-brand-border/70 pb-5">
                        <h3 class="min-w-0 truncate text-lg font-bold text-slate-950">{{ $level->name }}</h3>
                        <div class="flex items-center gap-2">
                            @if ($level->isRequired())
                                <span class="rounded-full bg-brand-accent/10 px-2.5 py-1 text-xs font-bold text-brand-primary">Required</span>
                            @endif
                            <span class="rounded-full px-2.5 py-1 text-xs font-bold {{ $level->is_active ? 'bg-emerald-50 text-emerald-800' : 'bg-slate-100 text-slate-600' }}">{{ $level->is_active ? 'Active' : 'Inactive' }}</span>
                        </div>
                    </div>

                    <div class="mt-5 grid gap-5 sm:grid-cols-2">
                        <div class="sm:col-span-2">
                            <label for="level-name-{{ $level->id }}" class="text-sm font-bold text-slate-800">Name</label>
                            <input id="level-name-{{ $level->id }}" name="name" value="{{ $levelBag->any() ? old('name') : $level->name }}" required maxlength="255" @readonly($level->isRequired()) class="mt-1.5 w-full rounded-lg border border-brand-border px-3.5 py-2.5 text-sm font-semibold outline-none focus:border-brand-primary focus:ring-2 focus:ring-brand-border {{ $level->isRequired() ? 'bg-slate-100 text-slate-600' : 'bg-white text-slate-900' }}">
                            @error('name', 'level-'.$level->id)<p class="mt-1 text-xs font-medium text-red-700">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label for="fee-{{ $level->id }}" class="text-sm font-bold text-slate-800">Fee</label>
                            <input id="fee-{{ $level->id }}" name="fee" type="number" min="0" max="99999999.99" step="0.01" value="{{ $levelBag->any() ? old('fee') : $level->fee }}" class="mt-1.5 w-full rounded-lg border border-brand-border bg-white px-3.5 py-2.5 text-sm font-semibold outline-none focus:border-brand-primary focus:ring-2 focus:ring-brand-border">
                            @error('fee', 'level-'.$level->id)<p class="mt-1 text-xs font-medium text-red-700">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label for="validity-{{ $level->id }}" class="text-sm font-bold text-slate-800">Validity days</label>
                            <input id="validity-{{ $level->id }}" name="validity_days" type="number" min="1" max="3650" value="{{ $levelBag->any() ? old('validity_days') : $level->validity_days }}" class="mt-1.5 w-full rounded-lg border border-brand-border bg-white px-3.5 py-2.5 text-sm font-semibold outline-none focus:border-brand-primary focus:ring-2 focus:ring-brand-border">
                            @error('validity_days', 'level-'.$level->id)<p class="mt-1 text-xs font-medium text-red-700">{{ $message }}</p>@enderror
                        </div>
                        <div class="sm:col-span-2">
                            <label for="benefits-{{ $level->id }}" class="text-sm font-bold text-slate-800">Benefits</label>
                            <textarea id="benefits-{{ $level->id }}" name="benefits" rows="3" maxlength="2000" class="mt-1.5 w-full rounded-lg border border-brand-border bg-white px-3.5 py-2.5 text-sm font-medium outline-none focus:border-brand-primary focus:ring-2 focus:ring-brand-border">{{ $levelBag->any() ? old('benefits') : $level->benefits }}</textarea>
                            @error('benefits', 'level-'.$level->id)<p class="mt-1 text-xs font-medium text-red-700">{{ $message }}</p>@enderror
                        </div>
                        @unless ($level->isRequired())
                            <label class="inline-flex items-center gap-2 text-sm font-semibold text-slate-700 sm:col-span-2">
                                <input name="is_active" type="checkbox" value="1" @checked($levelBag->any() ? old('is_active') : $level->is_active) class="size-4 rounded border-brand-border text-brand-primary focus:ring-brand-accent">
                                Active and available for new transactions
                            </label>
                        @endunless
                        @error('is_active', 'level-'.$level->id)<p class="text-xs font-medium text-red-700 sm:col-span-2">{{ $message }}</p>@enderror
                    </div>

                    <div class="mt-5 flex flex-wrap justify-end gap-2 border-t border-brand-border/70 pt-4">
                        @unless ($level->isRequired())
                            <button type="button" onclick="document.getElementById('delete-level-{{ $level->id }}').showModal()" class="rounded-lg border border-red-200 bg-red-50 px-4 py-2.5 text-sm font-semibold text-red-700 hover:bg-red-100">Delete</button>
                        @endunless
                        <button type="submit" class="rounded-xl bg-brand-primary px-4 py-2.5 text-sm font-bold text-white transition-[background-color,transform] duration-150 hover:bg-brand-sidebar active:scale-[0.98]">Save level</button>
                    </div>
                </form>
            @endforeach
        </div>
    </section>

    @endif
    @if ($page === 'categories')
    @foreach ($categories->reject->isRequired() as $category)
        @include('membership-settings._delete-modal', [
            'modalId' => 'delete-category-'.$category->id,
            'recordName' => $category->name.' category',
            'warning' => 'The category can be deleted only when no member is assigned to it.',
            'action' => route('membership-settings.categories.destroy', $category),
        ])
    @endforeach
    @else
    @foreach ($levels->reject->isRequired() as $level)
        @include('membership-settings._delete-modal', [
            'modalId' => 'delete-level-'.$level->id,
            'recordName' => $level->name.' membership level',
            'warning' => 'The level can be deleted only when it is not assigned to any member and is not referenced by a transaction.',
            'action' => route('membership-settings.levels.destroy', $level),
        ])
    @endforeach
    @endif
    @if ($page === 'categories' && $categoryCreateBag->any())
        <script>document.getElementById('add-category-modal').showModal();</script>
    @elseif ($page === 'levels' && $levelCreateBag->any())
        <script>document.getElementById('add-level-modal').showModal();</script>
    @endif
@endsection

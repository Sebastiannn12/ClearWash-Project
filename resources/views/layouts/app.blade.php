<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Members') · ClearWash</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-50 font-sans text-slate-900 antialiased">
    <div class="min-h-screen lg:flex">
        <aside class="hidden w-64 shrink-0 flex-col bg-[#0F172A] px-5 py-7 text-slate-300 lg:flex">
            <a href="{{ route('members.index') }}" class="flex items-center gap-3 px-2 text-white">
                <span class="flex size-10 items-center justify-center rounded-xl bg-blue-700 text-lg font-bold">C</span>
                <span class="text-lg font-semibold tracking-tight">ClearWash</span>
            </a>
            <p class="mt-10 px-3 text-[11px] font-semibold uppercase tracking-[0.16em] text-slate-500">Workspace</p>
            <nav class="mt-3 space-y-1" aria-label="Main navigation">
                <a href="{{ route('members.index') }}" class="flex items-center gap-3 rounded-xl px-3 py-3 text-sm font-semibold transition-colors {{ request()->routeIs('members.*') || request()->routeIs('home') ? 'bg-white/10 text-white' : 'text-slate-400 hover:bg-white/5 hover:text-white' }}">
                    <svg class="size-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M16 19a4 4 0 0 0-8 0m8 0H8m8 0h3a2 2 0 0 0 2-2v-1a4 4 0 0 0-3-3.87M8 19H5a2 2 0 0 1-2-2v-1a4 4 0 0 1 3-3.87M12 12a4 4 0 1 0 0-8 4 4 0 0 0 0 8Z"/></svg>
                    Members
                </a>
                <a href="{{ route('membership-settings.index') }}" class="flex items-center gap-3 rounded-xl px-3 py-3 text-sm font-semibold transition-colors {{ request()->routeIs('membership-settings.*') ? 'bg-white/10 text-white' : 'text-slate-400 hover:bg-white/5 hover:text-white' }}">
                    <svg class="size-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M12 3v18m6-15H9a3 3 0 0 0 0 6h6a3 3 0 0 1 0 6H6"/></svg>
                    Membership setup
                </a>
            </nav>
            <div class="mt-auto border-t border-white/10 px-3 pt-5 text-xs leading-5 text-slate-500">Member management<br>Car wash subscription system</div>
        </aside>

        <div class="min-w-0 flex-1">
            <header class="border-b border-slate-200 bg-white">
                <div class="mx-auto flex max-w-7xl items-center justify-between gap-4 px-5 py-4 sm:px-8 lg:px-10">
                    <div class="flex items-center gap-3">
                        <span class="flex size-9 items-center justify-center rounded-lg bg-[#0F172A] font-bold text-white lg:hidden">C</span>
                        <span class="text-sm font-medium text-slate-500">Workspace <span class="mx-2 text-slate-300">/</span> <span class="font-semibold text-slate-800">{{ request()->routeIs('membership-settings.*') ? 'Membership setup' : 'Members' }}</span></span>
                    </div>
                    @if (! request()->routeIs('membership-settings.*'))
                        <a href="{{ route('members.create') }}" class="hidden rounded-lg bg-[#1E3A8A] px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition-colors hover:bg-blue-900 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-700 sm:inline-flex">+ Add member</a>
                    @endif
                </div>
            </header>

            <main class="mx-auto max-w-7xl px-5 py-8 sm:px-8 lg:px-10 lg:py-10">
                @if (session('success'))
                    <div role="status" class="mb-6 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800">{{ session('success') }}</div>
                @endif
                @yield('content')
            </main>
        </div>
    </div>
</body>
</html>

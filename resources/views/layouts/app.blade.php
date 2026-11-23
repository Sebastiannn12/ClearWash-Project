<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Members') · ClearWash</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-brand-canvas font-sans text-slate-900 antialiased">
    <div class="min-h-screen lg:flex">
        <aside class="hidden h-screen w-64 shrink-0 flex-col bg-brand-sidebar px-5 py-7 text-slate-300 lg:sticky lg:top-0 lg:flex">
            <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-2 text-white">
                <span class="flex size-10 items-center justify-center rounded-xl bg-brand-primary text-lg font-bold">C</span>
                <span class="text-lg font-semibold tracking-tight">ClearWash</span>
            </a>
            <p class="mt-10 px-3 text-[11px] font-semibold uppercase tracking-[0.16em] text-slate-500">Workspace</p>
            <nav class="mt-3 space-y-1" aria-label="Main navigation">
                <a href="{{ route('dashboard') }}" class="flex items-center gap-3 rounded-xl px-3 py-3 text-sm font-semibold transition-colors {{ request()->routeIs('dashboard') ? 'bg-white/10 text-white' : 'text-slate-400 hover:bg-white/5 hover:text-white' }}">
                    <svg class="size-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M3 3h8v8H3zM13 3h8v5h-8zM13 10h8v11h-8zM3 13h8v8H3z"/></svg>
                    Dashboard
                </a>
                <a href="{{ route('members.index') }}" class="flex items-center gap-3 rounded-xl px-3 py-3 text-sm font-semibold transition-colors {{ request()->routeIs('members.*') || request()->routeIs('home') ? 'bg-white/10 text-white' : 'text-slate-400 hover:bg-white/5 hover:text-white' }}">
                    <svg class="size-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M16 19a4 4 0 0 0-8 0m8 0H8m8 0h3a2 2 0 0 0 2-2v-1a4 4 0 0 0-3-3.87M8 19H5a2 2 0 0 1-2-2v-1a4 4 0 0 1 3-3.87M12 12a4 4 0 1 0 0-8 4 4 0 0 0 0 8Z"/></svg>
                    Members
                </a>
                <a href="{{ route('transactions.index') }}" class="flex items-center gap-3 rounded-xl px-3 py-3 text-sm font-semibold transition-colors {{ request()->routeIs('transactions.*') ? 'bg-white/10 text-white' : 'text-slate-400 hover:bg-white/5 hover:text-white' }}">
                    <svg class="size-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M6 7h12M6 12h12M6 17h7M4 3h16a1 1 0 0 1 1 1v16a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V4a1 1 0 0 1 1-1Z"/></svg>
                    Transactions
                </a>
                <a href="{{ route('payments.index') }}" class="flex items-center gap-3 rounded-xl px-3 py-3 text-sm font-semibold transition-colors {{ request()->routeIs('payments.*') ? 'bg-white/10 text-white' : 'text-slate-400 hover:bg-white/5 hover:text-white' }}">Payments</a>
                <a href="{{ route('membership-settings.categories.index') }}" class="block rounded-xl px-3 py-2 text-sm font-semibold {{ request()->routeIs('membership-settings.categories.*') ? 'bg-white/10 text-white' : 'text-slate-400 hover:bg-white/5 hover:text-white' }}">Categories</a>
                <a href="{{ route('membership-settings.levels.index') }}" class="block rounded-xl px-3 py-2 text-sm font-semibold {{ request()->routeIs('membership-settings.levels.*') ? 'bg-white/10 text-white' : 'text-slate-400 hover:bg-white/5 hover:text-white' }}">Levels</a>
            </nav>
            <div class="mt-auto border-t border-white/10 px-3 pt-5 text-xs leading-5 text-slate-500">Member management<br>Car wash subscription system</div>
        </aside>

        <div class="min-w-0 flex-1 bg-white">
            <header class="border-b border-brand-border bg-brand-accent/10">
                <div class="mx-auto flex max-w-7xl items-center justify-between gap-4 px-5 py-4 sm:px-8 lg:px-10">
                    <div class="flex items-center gap-3">
                        <span class="flex size-9 items-center justify-center rounded-lg bg-brand-sidebar font-bold text-white lg:hidden">C</span>
                        <span class="text-sm font-medium text-slate-500"><span class="hidden sm:inline">Workspace <span class="mx-2 text-slate-300">/</span></span><span class="font-semibold text-slate-800">{{ request()->routeIs('dashboard') ? 'Dashboard' : (request()->routeIs('membership-settings.categories.*') ? 'Categories' : (request()->routeIs('membership-settings.levels.*') ? 'Levels' : (request()->routeIs('payments.*') ? 'Payments' : (request()->routeIs('transactions.*') ? 'Transactions' : 'Members')))) }}</span></span>
                    </div>
                    <div class="flex items-center gap-3">
                        @if (request()->routeIs('transactions.index'))
                            <a href="{{ route('transactions.create') }}" class="hidden rounded-lg bg-brand-primary px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition-colors hover:bg-brand-sidebar focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-accent sm:inline-flex">+ Create Transaction</a>
                        @endif
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="rounded-lg border border-brand-border bg-white px-3 py-2 text-sm font-semibold text-slate-700 transition-colors hover:bg-brand-canvas focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-accent">Log out</button>
                        </form>
                    </div>
                </div>
                <nav class="overflow-x-auto border-t border-brand-border/70 px-5 [scrollbar-width:none] [&::-webkit-scrollbar]:hidden lg:hidden" aria-label="Mobile navigation">
                    <div class="flex min-w-max gap-1 py-2">
                        <a href="{{ route('dashboard') }}" class="rounded-lg px-3 py-2 text-sm font-semibold transition-colors {{ request()->routeIs('dashboard') ? 'bg-brand-accent/10 text-brand-sidebar' : 'text-slate-500 hover:bg-brand-canvas hover:text-slate-900' }}">Dashboard</a>
                        <a href="{{ route('members.index') }}" class="rounded-lg px-3 py-2 text-sm font-semibold transition-colors {{ request()->routeIs('members.*') || request()->routeIs('home') ? 'bg-brand-accent/10 text-brand-sidebar' : 'text-slate-500 hover:bg-brand-canvas hover:text-slate-900' }}">Members</a>
                        <a href="{{ route('transactions.index') }}" class="rounded-lg px-3 py-2 text-sm font-semibold transition-colors {{ request()->routeIs('transactions.*') ? 'bg-brand-accent/10 text-brand-sidebar' : 'text-slate-500 hover:bg-brand-canvas hover:text-slate-900' }}">Transactions</a>
                        <a href="{{ route('payments.index') }}" class="rounded-lg px-3 py-2 text-sm font-semibold {{ request()->routeIs('payments.*') ? 'bg-brand-accent/10 text-brand-sidebar' : 'text-slate-500' }}">Payments</a>
                        <a href="{{ route('membership-settings.categories.index') }}" class="rounded-lg px-3 py-2 text-sm font-semibold text-brand-sidebar">Categories</a>
                        <a href="{{ route('membership-settings.levels.index') }}" class="rounded-lg px-3 py-2 text-sm font-semibold text-brand-sidebar">Levels</a>
                    </div>
                </nav>
            </header>

            <main class="mx-auto max-w-7xl px-5 py-7 sm:px-8 sm:py-9 lg:px-10 lg:py-12">
                @if (session('success'))
                    <div role="status" class="mb-6 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800">{{ session('success') }}</div>
                @endif
                @yield('content')
            </main>
        </div>
    </div>
</body>
</html>

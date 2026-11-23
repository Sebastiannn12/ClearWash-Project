<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Members') · ClearWash</title>
    <script>try{document.documentElement.classList.toggle('dark',localStorage.getItem('clearwash-theme')==='dark')}catch(e){}</script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-brand-canvas font-sans text-slate-900 antialiased">
    <a href="#main-content" class="fixed left-4 top-4 z-50 -translate-y-24 rounded-xl bg-brand-primary px-4 py-3 text-sm font-semibold text-white focus:translate-y-0">Skip to content</a>
    <div class="min-h-screen lg:flex">
        <aside class="cw-sidebar hidden h-screen w-64 shrink-0 flex-col bg-brand-sidebar px-5 py-7 text-slate-300 lg:sticky lg:top-0 lg:flex">
            <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-2 text-white">
                <span class="flex size-10 items-center justify-center rounded-xl bg-brand-primary text-lg font-bold">C</span>
                <span class="text-lg font-semibold tracking-tight">ClearWash</span>
            </a>
            <p class="mt-10 px-3 text-[11px] font-semibold uppercase tracking-[0.16em] text-slate-500">Workspace</p>
            <nav class="mt-3 space-y-1" aria-label="Main navigation">
                <a href="{{ route('dashboard') }}" class="flex items-center gap-3 rounded-xl px-3 py-3 text-sm font-semibold transition-colors {{ request()->routeIs('dashboard') ? 'bg-brand-primary text-white' : 'text-slate-400 hover:bg-white/5 hover:text-white' }}">
                    <svg class="size-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M3 3h8v8H3zM13 3h8v5h-8zM13 10h8v11h-8zM3 13h8v8H3z"/></svg>
                    Dashboard
                </a>
                <a href="{{ route('members.index') }}" class="flex items-center gap-3 rounded-xl px-3 py-3 text-sm font-semibold transition-colors {{ request()->routeIs('members.*') || request()->routeIs('home') ? 'bg-brand-primary text-white' : 'text-slate-400 hover:bg-white/5 hover:text-white' }}">
                    <svg class="size-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M16 19a4 4 0 0 0-8 0m8 0H8m8 0h3a2 2 0 0 0 2-2v-1a4 4 0 0 0-3-3.87M8 19H5a2 2 0 0 1-2-2v-1a4 4 0 0 1 3-3.87M12 12a4 4 0 1 0 0-8 4 4 0 0 0 0 8Z"/></svg>
                    Members
                </a>
                <a href="{{ route('transactions.index') }}" class="flex items-center gap-3 rounded-xl px-3 py-3 text-sm font-semibold transition-colors {{ request()->routeIs('transactions.*') ? 'bg-brand-primary text-white' : 'text-slate-400 hover:bg-white/5 hover:text-white' }}">
                    <svg class="size-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M6 7h12M6 12h12M6 17h7M4 3h16a1 1 0 0 1 1 1v16a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V4a1 1 0 0 1 1-1Z"/></svg>
                    Transactions
                </a>
                <a href="{{ route('payments.index') }}" class="flex items-center gap-3 rounded-xl px-3 py-3 text-sm font-semibold transition-colors {{ request()->routeIs('payments.*') ? 'bg-brand-primary text-white' : 'text-slate-400 hover:bg-white/5 hover:text-white' }}"><svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><rect x="3" y="5" width="18" height="14" rx="3"/><path d="M3 10h18m-5 5h2"/></svg>Payments</a>
                <a href="{{ route('membership-settings.categories.index') }}" class="flex items-center gap-3 rounded-xl px-3 py-3 text-sm font-semibold {{ request()->routeIs('membership-settings.categories.*') ? 'bg-brand-primary text-white' : 'text-slate-400 hover:bg-white/5 hover:text-white' }}"><svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><rect x="3" y="3" width="7" height="7" rx="1.5"/><rect x="14" y="3" width="7" height="7" rx="1.5"/><rect x="3" y="14" width="7" height="7" rx="1.5"/><rect x="14" y="14" width="7" height="7" rx="1.5"/></svg>Categories</a>
                <a href="{{ route('membership-settings.levels.index') }}" class="flex items-center gap-3 rounded-xl px-3 py-3 text-sm font-semibold {{ request()->routeIs('membership-settings.levels.*') ? 'bg-brand-primary text-white' : 'text-slate-400 hover:bg-white/5 hover:text-white' }}"><svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="m12 3 10 5-10 5L2 8l10-5Zm-9 9 9 5 9-5M3 16l9 5 9-5"/></svg>Levels</a>
            </nav>
            <div class="mt-auto border-t border-white/10 px-3 pt-5 text-xs leading-5 text-slate-500">Member management<br>Car wash subscription system</div>
        </aside>

        <div class="cw-app-pane min-w-0 flex-1 bg-white">
            <header class="cw-header border-b border-brand-border">
                <div class="mx-auto flex max-w-7xl items-center justify-between gap-4 px-5 py-4 sm:px-8 lg:px-10">
                    <div class="flex items-center gap-3">
                        <span class="flex size-9 items-center justify-center rounded-lg bg-brand-sidebar font-bold text-white lg:hidden">C</span>
                        <span class="hidden text-sm font-medium text-slate-500 sm:inline"><span>Workspace <span class="mx-2 text-slate-300">/</span></span><span class="font-semibold text-slate-800">{{ request()->routeIs('dashboard') ? 'Dashboard' : (request()->routeIs('membership-settings.categories.*') ? 'Categories' : (request()->routeIs('membership-settings.levels.*') ? 'Levels' : (request()->routeIs('payments.*') ? 'Payments' : (request()->routeIs('transactions.*') ? 'Transactions' : 'Members')))) }}</span></span>
                    </div>
                    <div class="flex items-center gap-3">
                        @if (request()->routeIs('transactions.index'))
                            <a href="{{ route('transactions.create') }}" class="hidden rounded-lg bg-brand-primary px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition-colors hover:bg-brand-sidebar focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-accent sm:inline-flex">+ Create Transaction</a>
                        @endif
                        <button type="button" class="cw-theme-toggle" data-theme-toggle aria-pressed="false"><svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M20 15.5A8 8 0 0 1 8.5 4 8 8 0 1 0 20 15.5Z"/></svg><span class="hidden sm:inline" data-theme-label>Dark mode</span><span class="sr-only sm:hidden" data-theme-label>Dark mode</span></button>
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

            <main id="main-content" class="cw-workspace mx-auto w-full max-w-[1600px] px-4 py-6 sm:px-7 sm:py-8 lg:px-10 lg:py-10">
                @if (session('success'))
                    <div role="status" class="mb-6 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800">{{ session('success') }}</div>
                @endif
                @yield('content')
            </main>
        </div>
    </div>
</body>
</html>

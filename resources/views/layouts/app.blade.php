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
            @include('layouts._sidebar-content')
        </aside>

        <div class="cw-app-pane min-w-0 flex-1 bg-white">
            <header class="cw-header sticky top-0 z-30 border-b border-brand-border">
                <div class="mx-auto flex max-w-[1600px] items-center justify-between gap-4 px-4 py-4 sm:px-7 lg:px-10">
                    <div class="flex items-center gap-3">
                        <button type="button" id="mobile-nav-trigger" class="flex size-11 shrink-0 items-center justify-center rounded-xl border border-brand-border text-brand-primary lg:hidden" aria-label="Open navigation" aria-expanded="false" aria-controls="mobile-navigation" aria-haspopup="dialog"><svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" d="M4 6h16M4 12h16M4 18h16"/></svg></button>
                        <img src="{{ asset('clearwash-logo-themed.png') }}" alt="ClearWash" class="h-9 w-auto max-w-36 object-contain dark:hidden lg:hidden">
                        <img src="{{ asset('clearwash-logo-dark.png') }}" alt="ClearWash" class="hidden h-9 w-auto max-w-36 object-contain dark:block lg:dark:hidden">
                        <span class="hidden text-sm font-medium text-slate-500 sm:inline"><span>Workspace <span class="mx-2 text-slate-300">/</span></span><span class="font-semibold text-slate-800">{{ request()->routeIs('dashboard') ? 'Dashboard' : (request()->routeIs('membership-settings.categories.*') ? 'Categories' : (request()->routeIs('membership-settings.levels.*') ? 'Levels' : (request()->routeIs('payments.*') ? 'Payments' : (request()->routeIs('transactions.*') ? 'Transactions' : 'Members')))) }}</span></span>
                    </div>
                    <div class="hidden items-center gap-3 lg:flex">
                        <button type="button" class="cw-theme-toggle" data-theme-toggle aria-pressed="false"><svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M20 15.5A8 8 0 0 1 8.5 4 8 8 0 1 0 20 15.5Z"/></svg><span class="hidden sm:inline" data-theme-label>Dark mode</span><span class="sr-only sm:hidden" data-theme-label>Dark mode</span></button>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="rounded-lg border border-brand-border bg-white px-3 py-2 text-sm font-semibold text-slate-700 transition-colors hover:bg-brand-canvas focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-accent">Log out</button>
                        </form>
                    </div>
                </div>
            </header>

            <main id="main-content" class="cw-workspace mx-auto w-full max-w-[1600px] px-4 py-6 sm:px-7 sm:py-8 lg:px-10 lg:py-10">
                @if (session('success'))
                    <div role="status" class="mb-6 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800">{{ session('success') }}</div>
                @endif
                @yield('content')
            </main>
        </div>
    </div>
    <dialog id="mobile-navigation" class="cw-mobile-navigation" aria-label="ClearWash navigation">
        <div class="cw-drawer-panel">
            <button type="button" class="cw-drawer-close" data-nav-close aria-label="Close navigation" autofocus><svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" d="m6 6 12 12M18 6 6 18"/></svg></button>
            <div class="cw-sidebar flex min-h-0 flex-1 flex-col gap-0">
                @include('layouts._sidebar-content')
            </div>
            <div class="mt-5 flex flex-wrap items-center gap-3 border-t border-white/20 pt-5">
                <button type="button" class="cw-theme-toggle" data-theme-toggle aria-pressed="false"><span data-theme-label>Dark mode</span></button>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="min-h-11 rounded-xl border border-white/30 px-4 py-2 text-sm font-semibold text-white hover:bg-white/10">Log out</button>
                </form>
                <a href="{{ route('transactions.create') }}" class="min-h-11 content-center text-sm font-semibold text-white underline-offset-4 hover:underline">Create transaction →</a>
            </div>
        </div>
    </dialog>
</body>
</html>

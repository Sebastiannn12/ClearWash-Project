<a href="{{ route('dashboard') }}" class="flex items-center px-2 text-white">
    <img src="{{ asset('clearwash-logo-dark.png') }}" alt="ClearWash" class="h-10 w-auto max-w-48 object-contain">
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
@php($adminNameParts = preg_split('/\s+/', trim(auth()->user()->name)))
<div class="mt-auto flex min-w-0 items-center gap-3 border-t border-white/20 px-2 pt-5">
    <span class="flex size-10 shrink-0 items-center justify-center rounded-full bg-brand-primary text-sm font-bold text-white" aria-hidden="true">{{ \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr($adminNameParts[0] ?? '', 0, 1).(count($adminNameParts) > 1 ? \Illuminate\Support\Str::substr(end($adminNameParts), 0, 1) : '')) }}</span>
    <div class="min-w-0"><p class="truncate text-sm font-semibold text-white">{{ auth()->user()->name }}</p><p class="truncate text-xs text-slate-300">{{ auth()->user()->email }}</p></div>
</div>

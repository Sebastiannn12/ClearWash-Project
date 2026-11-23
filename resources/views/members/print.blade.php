<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Membership record · {{ $member->membership_number }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-brand-canvas p-5 font-sans text-brand-sidebar sm:p-10 print:bg-white print:p-0">
    <main class="mx-auto max-w-2xl rounded-2xl border border-brand-border bg-white p-6 shadow-sm sm:p-10 print:border-0 print:shadow-none">
        <div class="flex items-start justify-between gap-4 border-b border-brand-border pb-6">
            <div><p class="text-sm font-bold uppercase tracking-widest text-brand-accent">ClearWash</p><h1 class="mt-2 text-2xl font-bold">Membership record</h1></div>
            <button type="button" onclick="window.print()" class="rounded-xl bg-brand-primary px-4 py-2 text-sm font-bold text-white print:hidden">Print</button>
        </div>
        <dl class="mt-8 grid gap-6 sm:grid-cols-2">
            <div><dt class="text-xs font-bold uppercase text-slate-500">Member</dt><dd class="mt-1 font-semibold">{{ $member->full_name }}</dd></div>
            <div><dt class="text-xs font-bold uppercase text-slate-500">Membership number</dt><dd class="mt-1 font-mono font-semibold">{{ $member->membership_number }}</dd></div>
            <div><dt class="text-xs font-bold uppercase text-slate-500">Category</dt><dd class="mt-1">{{ $member->membershipCategory?->name ?? $member->category }}</dd></div>
            <div><dt class="text-xs font-bold uppercase text-slate-500">Level</dt><dd class="mt-1">{{ $member->membershipLevel?->name ?? $member->membership_level }}</dd></div>
            <div><dt class="text-xs font-bold uppercase text-slate-500">Joined</dt><dd class="mt-1">{{ $member->joined_on->format('M j, Y') }}</dd></div>
            <div><dt class="text-xs font-bold uppercase text-slate-500">Status</dt><dd class="mt-1">{{ $member->is_active ? 'Active' : 'Inactive' }}</dd></div>
        </dl>
        <p class="mt-10 border-t border-brand-border pt-5 text-xs text-slate-500">Printed {{ now()->format('M j, Y g:i A') }} · ClearWash</p>
    </main>
</body>
</html>

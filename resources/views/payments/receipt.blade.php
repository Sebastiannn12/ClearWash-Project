<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Payment receipt · {{ $payment->payment_reference }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-brand-canvas p-5 font-sans text-slate-900 sm:p-10 print:bg-white print:p-0">
    @php($transaction = $payment->membershipTransaction)
    @php($member = $transaction->member)
    <main class="mx-auto max-w-2xl rounded-2xl border border-brand-border bg-white p-6 shadow-sm sm:p-10 print:border-0 print:shadow-none">
        <header class="flex items-start justify-between gap-4 border-b border-brand-border pb-6">
            <div><p class="text-sm font-bold uppercase tracking-widest text-brand-primary">ClearWash</p><h1 class="mt-2 text-2xl font-bold">Payment receipt</h1><p class="mt-1 text-sm text-slate-600">{{ $payment->paid_at->format('M j, Y g:i A') }}</p></div>
            <button type="button" onclick="window.print()" class="rounded-xl bg-brand-primary px-4 py-2 text-sm font-bold text-white print:hidden">Print</button>
        </header>
        <dl class="mt-7 grid gap-5 text-sm sm:grid-cols-2">
            <div><dt class="font-semibold text-slate-600">Member</dt><dd class="mt-1 font-bold">{{ $member->full_name }}</dd></div>
            <div><dt class="font-semibold text-slate-600">Membership number</dt><dd class="mt-1 break-all font-mono">{{ $member->membership_number }}</dd></div>
            <div><dt class="font-semibold text-slate-600">Category</dt><dd class="mt-1">{{ $transaction->category_name ?? $member->membershipCategory?->name ?? $member->category }}</dd></div>
            <div><dt class="font-semibold text-slate-600">Level</dt><dd class="mt-1">{{ $transaction->level_name ?? $transaction->membershipLevel?->name }}</dd></div>
            <div><dt class="font-semibold text-slate-600">Transaction reference</dt><dd class="mt-1 break-all font-mono">{{ $transaction->reference_number }}</dd></div>
            <div><dt class="font-semibold text-slate-600">Payment reference</dt><dd class="mt-1 break-all font-mono">{{ $payment->payment_reference ?: '—' }}</dd></div>
            <div><dt class="font-semibold text-slate-600">Payment method</dt><dd class="mt-1">{{ $payment->method }}</dd></div>
            <div><dt class="font-semibold text-slate-600">Transaction date</dt><dd class="mt-1">{{ $transaction->created_at->format('M j, Y') }}</dd></div>
        </dl>
        <dl class="mt-7 space-y-3 border-t border-brand-border pt-6 text-sm">
            <div class="flex justify-between gap-4"><dt>Original fee</dt><dd>₱{{ number_format((float) ($transaction->original_fee ?? $transaction->amount_due), 2) }}</dd></div>
            <div class="flex justify-between gap-4"><dt>Discount ({{ number_format((float) $transaction->discount_percentage, 2) }}%)</dt><dd>−₱{{ number_format((float) $transaction->discount_amount, 2) }}</dd></div>
            <div class="flex justify-between gap-4"><dt>Transaction amount due</dt><dd>₱{{ number_format((float) $transaction->amount_due, 2) }}</dd></div>
            <div class="flex justify-between gap-4 border-t border-brand-border pt-3 text-lg font-bold"><dt>Payment received</dt><dd>₱{{ number_format((float) $payment->amount, 2) }}</dd></div>
        </dl>
        @if ($payment->notes)<p class="mt-6 whitespace-pre-line text-sm text-slate-600">{{ $payment->notes }}</p>@endif
        <p class="mt-8 border-t border-brand-border pt-4 text-xs text-slate-500">Printed {{ now()->format('M j, Y g:i A') }} · ClearWash</p>
    </main>
</body>
</html>

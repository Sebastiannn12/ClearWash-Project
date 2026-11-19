@extends('layouts.app')

@section('title', $transaction->reference_number)

@section('content')
    <a href="{{ route('transactions.index') }}" class="text-sm font-semibold text-blue-900 hover:underline">← Back to transactions</a>

    <div class="mt-5 flex flex-wrap items-start justify-between gap-4">
        <div>
            <p class="text-xs font-bold uppercase tracking-[0.15em] text-blue-800">Transaction details</p>
            <h1 class="mt-2 font-mono text-3xl font-bold tracking-tight text-slate-950">{{ $transaction->reference_number }}</h1>
            <div class="mt-3 flex flex-wrap items-center gap-3">
                <span class="text-sm font-semibold text-slate-600">{{ $transaction->type }}</span>
                <span class="rounded-full bg-blue-50 px-2.5 py-1 text-xs font-bold text-blue-800">{{ $transaction->status }}</span>
            </div>
        </div>
        @if (in_array($transaction->status, [\App\TransactionStatus::Pending->value, \App\TransactionStatus::Paid->value], true))
            <div class="flex flex-wrap gap-3">
                @if ($transaction->status === \App\TransactionStatus::Paid->value && $remainingBalance <= 0)
                    <form method="POST" action="{{ route('transactions.approve', $transaction) }}">
                        @csrf
                        @method('PATCH')
                        <button class="rounded-lg bg-[#1E3A8A] px-4 py-2.5 text-sm font-bold text-white shadow-sm hover:bg-blue-900">Approve</button>
                    </form>
                @endif
                <form method="POST" action="{{ route('transactions.cancel', $transaction) }}" onsubmit="return confirm('Cancel this transaction? Payment history will be retained.')">
                    @csrf
                    @method('PATCH')
                    <button class="rounded-lg border border-red-200 px-4 py-2.5 text-sm font-bold text-red-700 hover:bg-red-50">Cancel</button>
                </form>
            </div>
        @endif
    </div>

    @error('status')
        <p class="mt-4 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm font-semibold text-red-700">{{ $message }}</p>
    @enderror

    <div class="mt-8 grid max-w-6xl gap-6 lg:grid-cols-[minmax(0,2fr)_minmax(280px,1fr)]">
        <div class="space-y-6">
            <section class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm sm:p-7">
                <h2 class="text-lg font-bold text-slate-950">Transaction information</h2>
                <dl class="mt-6 grid gap-x-6 gap-y-6 sm:grid-cols-2">
                    <div>
                        <dt class="text-xs font-bold uppercase tracking-wide text-slate-600">Member</dt>
                        <dd class="mt-1 text-sm font-semibold text-slate-900">{{ $transaction->member->full_name }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-bold uppercase tracking-wide text-slate-600">Membership level</dt>
                        <dd class="mt-1 text-sm font-semibold text-slate-900">{{ $transaction->membershipLevel->name }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-bold uppercase tracking-wide text-slate-600">Transaction type</dt>
                        <dd class="mt-1 text-sm font-semibold text-slate-900">{{ $transaction->type }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-bold uppercase tracking-wide text-slate-600">Status</dt>
                        <dd class="mt-1 text-sm font-semibold text-slate-900">{{ $transaction->status }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-bold uppercase tracking-wide text-slate-600">Amount due</dt>
                        <dd class="mt-1 text-sm font-semibold text-slate-900">₱{{ number_format((float) $transaction->amount_due, 2) }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-bold uppercase tracking-wide text-slate-600">Validity</dt>
                        <dd class="mt-1 text-sm font-semibold text-slate-900">{{ $transaction->validity_days }} days</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-bold uppercase tracking-wide text-slate-600">Created</dt>
                        <dd class="mt-1 text-sm font-semibold text-slate-900">{{ $transaction->created_at->format('M j, Y g:i A') }}</dd>
                    </div>
                    @if ($transaction->approved_at)
                        <div><dt class="text-xs font-bold uppercase tracking-wide text-slate-600">Approved</dt><dd class="mt-1 text-sm font-semibold text-slate-900">{{ $transaction->approved_at->format('M j, Y g:i A') }}</dd></div>
                        <div><dt class="text-xs font-bold uppercase tracking-wide text-slate-600">Subscription ends</dt><dd class="mt-1 text-sm font-semibold text-slate-900">{{ $transaction->expires_at->format('M j, Y g:i A') }}</dd></div>
                    @endif
                </dl>

                <div class="mt-7 border-t border-slate-100 pt-6">
                    <h3 class="text-sm font-bold text-slate-950">Notes</h3>
                    <p class="mt-2 whitespace-pre-line text-sm font-medium leading-6 text-slate-700">{{ $transaction->notes ?: 'No notes added.' }}</p>
                </div>
            </section>

            <section class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
                <div class="border-b border-slate-100 px-5 py-5 sm:px-7">
                    <h2 class="text-lg font-bold text-slate-950">Payment history</h2>
                    <p class="mt-1 text-sm font-medium text-slate-600">Payments recorded for this transaction.</p>
                </div>

                @if ($transaction->payments->isEmpty())
                    <div class="px-5 py-10 text-center sm:px-7">
                        <p class="text-sm font-semibold text-slate-700">No payments recorded.</p>
                        <p class="mt-1 text-sm text-slate-500">Payment activity will appear here when available.</p>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-slate-200 text-left text-sm">
                            <thead class="bg-slate-50 text-xs font-bold uppercase tracking-wide text-slate-600">
                                <tr>
                                    <th class="px-5 py-3 sm:px-7">Reference</th>
                                    <th class="px-5 py-3">Method</th>
                                    <th class="px-5 py-3">Paid at</th>
                                    <th class="px-5 py-3 text-right sm:pr-7">Amount</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 text-slate-800">
                                @foreach ($transaction->payments as $payment)
                                    <tr>
                                        <td class="whitespace-nowrap px-5 py-4 font-mono font-semibold sm:px-7">{{ $payment->payment_reference ?: '—' }}</td>
                                        <td class="whitespace-nowrap px-5 py-4 font-semibold">{{ $payment->method }}</td>
                                        <td class="whitespace-nowrap px-5 py-4 font-medium">{{ $payment->paid_at->format('M j, Y g:i A') }}</td>
                                        <td class="whitespace-nowrap px-5 py-4 text-right font-bold sm:pr-7">₱{{ number_format((float) $payment->amount, 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </section>
        </div>

        <aside class="space-y-6">
            <section class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm sm:p-7">
                <h2 class="text-lg font-bold text-slate-950">Payment summary</h2>
                <dl class="mt-5 space-y-5">
                    <div class="flex items-center justify-between gap-4"><dt class="text-sm font-semibold text-slate-600">Amount due</dt><dd class="text-sm font-bold text-slate-950">₱{{ number_format((float) $transaction->amount_due, 2) }}</dd></div>
                    <div class="flex items-center justify-between gap-4"><dt class="text-sm font-semibold text-slate-600">Total paid</dt><dd class="text-sm font-bold text-emerald-700">₱{{ number_format($totalPaid, 2) }}</dd></div>
                    <div class="flex items-center justify-between gap-4 border-t border-slate-100 pt-5"><dt class="text-sm font-bold text-slate-800">Remaining</dt><dd class="text-base font-bold text-slate-950">₱{{ number_format($remainingBalance, 2) }}</dd></div>
                </dl>
            </section>

            @if ($transaction->status === \App\TransactionStatus::Pending->value && $remainingBalance > 0)
                <section class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm sm:p-7">
                    <h2 class="text-lg font-bold text-slate-950">Record payment</h2>
                    <p class="mt-1 text-sm font-medium text-slate-600">Add a full or partial payment.</p>

                    <form method="POST" action="{{ route('transactions.payments.store', $transaction) }}" class="mt-5 space-y-5">
                        @csrf
                        <input type="hidden" name="payment_reference" value="{{ old('payment_reference', $paymentSubmissionToken) }}">

                        @error('payment_reference')
                            <p class="rounded-lg border border-red-200 bg-red-50 px-3 py-2 text-xs font-semibold text-red-700">{{ $message }}</p>
                        @enderror

                        <div>
                            <label for="amount" class="text-sm font-bold text-slate-700">Amount <span class="text-red-600">*</span></label>
                            <input id="amount" name="amount" type="number" min="0.01" max="{{ number_format($remainingBalance, 2, '.', '') }}" step="0.01" value="{{ old('amount') }}" required class="mt-1.5 w-full rounded-lg border border-slate-300 bg-white px-3.5 py-2.5 text-sm font-medium text-slate-900 outline-none focus:border-blue-700 focus:ring-2 focus:ring-blue-100">
                            @error('amount')<p class="mt-1 text-xs text-red-700">{{ $message }}</p>@enderror
                        </div>

                        <div>
                            <label for="method" class="text-sm font-bold text-slate-700">Method <span class="text-red-600">*</span></label>
                            <select id="method" name="method" required class="mt-1.5 w-full rounded-lg border border-slate-300 bg-white px-3.5 py-2.5 text-sm font-semibold text-slate-900 outline-none focus:border-blue-700 focus:ring-2 focus:ring-blue-100">
                                <option value="">Select method</option>
                                @foreach (['Cash', 'GCash', 'Card', 'Bank'] as $method)
                                    <option value="{{ $method }}" @selected(old('method') === $method)>{{ $method }}</option>
                                @endforeach
                            </select>
                            @error('method')<p class="mt-1 text-xs text-red-700">{{ $message }}</p>@enderror
                        </div>

                        <div>
                            <label for="payment_notes" class="text-sm font-bold text-slate-700">Notes</label>
                            <textarea id="payment_notes" name="notes" rows="2" maxlength="1000" class="mt-1.5 w-full rounded-lg border border-slate-300 bg-white px-3.5 py-2.5 text-sm font-medium text-slate-900 outline-none focus:border-blue-700 focus:ring-2 focus:ring-blue-100">{{ old('notes') }}</textarea>
                            @error('notes')<p class="mt-1 text-xs text-red-700">{{ $message }}</p>@enderror
                        </div>

                        <button type="submit" class="w-full rounded-lg bg-[#1E3A8A] px-4 py-2.5 text-sm font-bold text-white shadow-sm hover:bg-blue-900">Record payment</button>
                    </form>
                </section>
            @endif

            <section class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm sm:p-7">
                <h2 class="text-lg font-bold text-slate-950">Reference</h2>
                <dl class="mt-5 space-y-5">
                    <div>
                        <dt class="text-xs font-bold uppercase tracking-wide text-slate-600">Transaction number</dt>
                        <dd class="mt-1 break-all font-mono text-sm font-semibold text-slate-900">{{ $transaction->reference_number }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-bold uppercase tracking-wide text-slate-600">Member number</dt>
                        <dd class="mt-1 font-mono text-sm font-semibold text-slate-900">{{ $transaction->member->membership_number ?: '—' }}</dd>
                    </div>
                </dl>
            </section>
        </aside>
    </div>
@endsection

@extends('layouts.app')

@section('title', 'Record payment')

@section('content')
    <a href="{{ route('transactions.show', $transaction) }}" class="text-sm font-semibold text-brand-primary hover:underline">Back to transaction</a>
    <div class="mt-6 max-w-xl">
        <p class="mb-4 text-sm text-slate-600">{{ $transaction->reference_number }} · {{ $transaction->member->full_name }} · Remaining ₱{{ number_format($remainingBalance, 2) }}</p>
                <section class="rounded-2xl border border-brand-border bg-brand-accent/10 p-5 sm:p-7">
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
                            <input id="amount" name="amount" type="number" min="0.01" max="{{ number_format($remainingBalance, 2, '.', '') }}" step="0.01" value="{{ old('amount') }}" required class="mt-2 w-full rounded-xl border border-brand-border bg-white px-3.5 py-3 text-sm font-medium text-slate-900 outline-none transition-[border-color,box-shadow] duration-150 focus:border-brand-primary focus:ring-3 focus:ring-brand-border">
                            @error('amount')<p class="mt-1 text-xs text-red-700">{{ $message }}</p>@enderror
                        </div>

                        <div>
                            <label for="method" class="text-sm font-bold text-slate-700">Method <span class="text-red-600">*</span></label>
                            <select id="method" name="method" required class="mt-2 w-full rounded-xl border border-brand-border bg-white px-3.5 py-3 text-sm font-semibold text-slate-900 outline-none transition-[border-color,box-shadow] duration-150 focus:border-brand-primary focus:ring-3 focus:ring-brand-border">
                                <option value="">Select method</option>
                                @foreach (['Cash', 'GCash', 'Card', 'Bank'] as $method)
                                    <option value="{{ $method }}" @selected(old('method') === $method)>{{ $method }}</option>
                                @endforeach
                            </select>
                            @error('method')<p class="mt-1 text-xs text-red-700">{{ $message }}</p>@enderror
                        </div>

                        <div>
                            <label for="payment_notes" class="text-sm font-bold text-slate-700">Notes</label>
                            <textarea id="payment_notes" name="notes" rows="2" maxlength="1000" class="mt-2 w-full rounded-xl border border-brand-border bg-white px-3.5 py-3 text-sm font-medium text-slate-900 outline-none transition-[border-color,box-shadow] duration-150 focus:border-brand-primary focus:ring-3 focus:ring-brand-border">{{ old('notes') }}</textarea>
                            @error('notes')<p class="mt-1 text-xs text-red-700">{{ $message }}</p>@enderror
                        </div>

                        <button type="submit" class="w-full rounded-xl bg-brand-primary px-4 py-3 text-sm font-bold text-white transition-[background-color,transform] duration-150 hover:bg-brand-sidebar active:scale-[0.98]">Record payment</button>
                    </form>
                </section>
    </div>
@endsection

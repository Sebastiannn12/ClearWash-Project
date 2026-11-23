<section id="record-payment" class="rounded-2xl border border-brand-border bg-white p-5 sm:p-7" aria-labelledby="record-payment-heading">
    <h2 id="record-payment-heading" class="text-lg font-bold text-slate-950">Record payment</h2>
    <p class="mt-1 text-sm text-slate-600">Accept a full or partial payment up to the remaining balance.</p>
    <form method="POST" action="{{ route('transactions.payments.store', $transaction) }}" class="mt-5 space-y-4">
        @csrf
        <input type="hidden" name="payment_reference" value="{{ old('payment_reference', $paymentSubmissionToken) }}">
        @error('payment_reference')<p class="text-xs font-semibold text-red-700">{{ $message }}</p>@enderror
        <div>
            <label for="amount" class="text-sm font-bold text-slate-700">Amount</label>
            <input id="amount" name="amount" type="number" min="0.01" max="{{ number_format($remainingBalance, 2, '.', '') }}" step="0.01" value="{{ old('amount') }}" required @error('amount') aria-invalid="true" @enderror class="mt-2 w-full rounded-xl border border-brand-border bg-white px-3.5 py-3 text-sm text-slate-900 focus:border-brand-primary focus:ring-2 focus:ring-brand-border">
            @error('amount')<p class="mt-1 text-xs text-red-700">{{ $message }}</p>@enderror
        </div>
        <div>
            <label for="method" class="text-sm font-bold text-slate-700">Method</label>
            <select id="method" name="method" required @error('method') aria-invalid="true" @enderror class="mt-2 w-full rounded-xl border border-brand-border bg-white px-3.5 py-3 text-sm text-slate-900 focus:border-brand-primary focus:ring-2 focus:ring-brand-border">
                <option value="">Select method</option>
                @foreach (['Cash', 'GCash', 'Card', 'Bank'] as $method)
                    <option value="{{ $method }}" @selected(old('method') === $method)>{{ $method }}</option>
                @endforeach
            </select>
            @error('method')<p class="mt-1 text-xs text-red-700">{{ $message }}</p>@enderror
        </div>
        <div>
            <label for="payment_notes" class="text-sm font-bold text-slate-700">Notes</label>
            <textarea id="payment_notes" name="notes" rows="2" maxlength="1000" class="mt-2 w-full rounded-xl border border-brand-border bg-white px-3.5 py-3 text-sm text-slate-900 focus:border-brand-primary focus:ring-2 focus:ring-brand-border">{{ old('notes') }}</textarea>
            @error('notes')<p class="mt-1 text-xs text-red-700">{{ $message }}</p>@enderror
        </div>
        <button type="submit" class="w-full rounded-xl bg-brand-primary px-4 py-3 text-sm font-bold text-white hover:bg-brand-sidebar">Record payment</button>
    </form>
</section>

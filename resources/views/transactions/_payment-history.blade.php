@if ($payments->isEmpty())
    <div class="px-5 py-8 text-center sm:px-7">
        <p class="text-sm font-semibold text-slate-700">No payments recorded.</p>
        <p class="mt-1 text-sm text-slate-500">Payment activity will appear here when available.</p>
    </div>
@else
    <div class="divide-y divide-brand-border lg:hidden">
        @foreach ($payments as $payment)
            <article class="cw-hover-record cw-stretched-row p-5 sm:px-7">
                <p class="break-all font-mono text-xs font-semibold text-slate-800">{{ $payment->payment_reference ?: 'No reference' }}</p>
                <p class="mt-2 text-sm text-slate-600">{{ $payment->method }} · {{ $payment->paid_at->format('M j, Y g:i A') }}</p>
                <div class="mt-3 flex flex-wrap items-center justify-between gap-3">
                    <strong class="text-lg text-slate-950">₱{{ number_format((float) $payment->amount, 2) }}</strong>
                    <a href="{{ route('payments.receipt', $payment) }}" target="_blank" rel="noopener" class="cw-stretched-link inline-flex min-h-11 items-center text-sm font-semibold text-brand-primary underline-offset-4" aria-label="Print receipt {{ $payment->payment_reference ?: 'for payment' }}">Receipt →</a>
                </div>
            </article>
        @endforeach
    </div>
    <div class="hidden overflow-x-auto lg:block">
        <table class="w-full min-w-[760px] divide-y divide-slate-200 text-left text-sm">
            <thead class="bg-brand-canvas text-xs font-bold uppercase tracking-wide text-slate-600">
                <tr>
                    <th class="px-5 py-3 sm:px-7">Reference</th>
                    <th class="px-5 py-3">Method</th>
                    <th class="px-5 py-3">Paid at</th>
                    <th class="px-5 py-3 text-right sm:pr-7">Amount</th>
                    <th class="px-5 py-3 text-right sm:pr-7">Receipt</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 text-slate-800">
                @foreach ($payments as $payment)
                    <tr class="cw-hover-record cw-stretched-row">
                        <td class="whitespace-nowrap px-5 py-4 font-mono font-semibold sm:px-7">{{ $payment->payment_reference ?: '—' }}</td>
                        <td class="whitespace-nowrap px-5 py-4 font-semibold">{{ $payment->method }}</td>
                        <td class="whitespace-nowrap px-5 py-4 font-medium">{{ $payment->paid_at->format('M j, Y g:i A') }}</td>
                        <td class="whitespace-nowrap px-5 py-4 text-right font-bold sm:pr-7">₱{{ number_format((float) $payment->amount, 2) }}</td>
                        <td class="whitespace-nowrap px-5 py-4 text-right sm:pr-7"><a href="{{ route('payments.receipt', $payment) }}" target="_blank" rel="noopener" class="cw-stretched-link font-semibold text-brand-primary underline-offset-4" aria-label="Print receipt {{ $payment->payment_reference ?: 'for payment' }}">Print</a></td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endif

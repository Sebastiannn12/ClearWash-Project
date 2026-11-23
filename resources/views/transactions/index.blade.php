@extends('layouts.app')

@section('title', 'Transactions')

@section('content')
    <div class="flex flex-wrap items-start justify-between gap-4">
        <div>
            <h1 class="text-3xl font-bold tracking-tight text-slate-950 sm:text-4xl">Transactions</h1>
            <p class="mt-3 text-sm font-medium leading-6 text-slate-600 sm:text-base">Review membership applications and renewals.</p>
        </div>
        <a href="{{ route('transactions.create') }}" class="rounded-xl bg-brand-primary px-4 py-2.5 text-sm font-bold text-white transition-[background-color,transform] duration-150 hover:bg-brand-sidebar active:scale-[0.98] sm:hidden">Create transaction</a>
    </div>

    <section class="mt-8 overflow-hidden rounded-2xl border border-brand-border bg-white">
        @if ($transactions->isEmpty())
            <div class="px-5 py-12 text-center sm:px-7">
                <p class="text-sm font-semibold text-slate-700">No transactions yet.</p>
                <p class="mt-1 text-sm text-slate-500">Create a transaction to start an application or renewal.</p>
            </div>
        @else
            <div class="divide-y divide-brand-border/50 xl:hidden">
                @foreach ($transactions as $transaction)
                    @php($statusClass = match ($transaction->status) {
                        \App\TransactionStatus::Approve->value => 'bg-emerald-50 text-emerald-800',
                        \App\TransactionStatus::Paid->value => 'bg-blue-50 text-blue-800',
                        \App\TransactionStatus::Cancelled->value => 'bg-slate-100 text-slate-600',
                        \App\TransactionStatus::Expired->value => 'bg-red-50 text-red-800',
                        default => 'bg-amber-50 text-amber-800',
                    })
                    <article class="p-5 sm:p-6">
                        <div class="flex flex-wrap items-start justify-between gap-3">
                            <div class="min-w-0">
                                <a href="{{ route('transactions.show', $transaction) }}" class="break-all font-mono text-sm font-bold text-brand-primary hover:underline focus-visible:outline-2 focus-visible:outline-brand-primary">{{ $transaction->reference_number }}</a>
                                <p class="mt-1 font-semibold text-slate-900">{{ $transaction->member->full_name }}</p>
                            </div>
                            <span class="rounded-full px-2.5 py-1 text-xs font-bold {{ $statusClass }}">{{ $transaction->status }}</span>
                        </div>
                        <dl class="mt-4 grid grid-cols-2 gap-4 text-sm sm:grid-cols-4">
                            <div><dt class="text-xs font-semibold text-slate-500">Level</dt><dd class="mt-1 font-medium text-slate-800">{{ $transaction->membershipLevel->name }}</dd></div>
                            <div><dt class="text-xs font-semibold text-slate-500">Type</dt><dd class="mt-1 font-medium text-slate-800">{{ $transaction->type }}</dd></div>
                            <div><dt class="text-xs font-semibold text-slate-500">Amount</dt><dd class="mt-1 font-bold text-slate-900">₱{{ number_format((float) $transaction->amount_due, 2) }}</dd></div>
                            <div><dt class="text-xs font-semibold text-slate-500">Created</dt><dd class="mt-1 font-medium text-slate-700">{{ $transaction->created_at->format('M j, Y') }}</dd></div>
                        </dl>
                    </article>
                @endforeach
            </div>
            <div class="hidden xl:block">
                <table class="cw-transactions-table w-full table-fixed divide-y divide-slate-200 text-left text-sm">
                    <thead class="bg-brand-canvas text-xs font-bold uppercase tracking-wide text-slate-600">
                        <tr>
                            <th class="px-5 py-3 sm:px-7">Reference</th>
                            <th class="px-5 py-3">Member</th>
                            <th class="px-5 py-3">Level</th>
                            <th class="px-5 py-3">Type</th>
                            <th class="px-5 py-3">Status</th>
                            <th class="px-5 py-3 text-right">Amount</th>
                            <th class="px-5 py-3 text-right sm:pr-7">Created</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-800">
                        @foreach ($transactions as $transaction)
                            @php($statusClass = match ($transaction->status) {
                                \App\TransactionStatus::Approve->value => 'bg-emerald-50 text-emerald-800',
                                \App\TransactionStatus::Paid->value => 'bg-blue-50 text-blue-800',
                                \App\TransactionStatus::Cancelled->value => 'bg-slate-100 text-slate-600',
                                \App\TransactionStatus::Expired->value => 'bg-red-50 text-red-800',
                                default => 'bg-amber-50 text-amber-800',
                            })
                            <tr class="hover:bg-brand-canvas">
                                <td class="whitespace-nowrap px-5 py-4 sm:px-7"><a href="{{ route('transactions.show', $transaction) }}" class="font-mono font-bold text-brand-primary hover:underline">{{ $transaction->reference_number }}</a></td>
                                <td class="whitespace-nowrap px-5 py-4 font-semibold">{{ $transaction->member->full_name }}</td>
                                <td class="whitespace-nowrap px-5 py-4 font-medium">{{ $transaction->membershipLevel->name }}</td>
                                <td class="whitespace-nowrap px-5 py-4 font-medium">{{ $transaction->type }}</td>
                                <td class="whitespace-nowrap px-5 py-4"><span class="rounded-full px-2.5 py-1 text-xs font-bold {{ $statusClass }}">{{ $transaction->status }}</span></td>
                                <td class="whitespace-nowrap px-5 py-4 text-right font-bold">₱{{ number_format((float) $transaction->amount_due, 2) }}</td>
                                <td class="whitespace-nowrap px-5 py-4 text-right font-medium text-slate-600 sm:pr-7">{{ $transaction->created_at->format('M j, Y') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </section>
@endsection

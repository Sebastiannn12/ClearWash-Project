@extends('layouts.app')

@section('title', 'Transactions')

@section('content')
    <div class="flex flex-wrap items-start justify-between gap-4">
        <div>
            <p class="text-xs font-bold uppercase tracking-[0.15em] text-blue-800">Transaction processing</p>
            <h1 class="mt-2 text-3xl font-bold tracking-tight text-slate-950">Transactions</h1>
            <p class="mt-2 text-sm font-medium text-slate-600">Review membership applications and renewals.</p>
        </div>
        <a href="{{ route('transactions.create') }}" class="rounded-lg bg-[#1E3A8A] px-4 py-2.5 text-sm font-bold text-white shadow-sm hover:bg-blue-900 sm:hidden">+ Create Transaction</a>
    </div>

    <section class="mt-8 overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
        @if ($transactions->isEmpty())
            <div class="px-5 py-12 text-center sm:px-7">
                <p class="text-sm font-semibold text-slate-700">No transactions yet.</p>
                <p class="mt-1 text-sm text-slate-500">Create a transaction to start an application or renewal.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200 text-left text-sm">
                    <thead class="bg-slate-50 text-xs font-bold uppercase tracking-wide text-slate-600">
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
                            <tr class="hover:bg-slate-50">
                                <td class="whitespace-nowrap px-5 py-4 sm:px-7"><a href="{{ route('transactions.show', $transaction) }}" class="font-mono font-bold text-blue-900 hover:underline">{{ $transaction->reference_number }}</a></td>
                                <td class="whitespace-nowrap px-5 py-4 font-semibold">{{ $transaction->member->full_name }}</td>
                                <td class="whitespace-nowrap px-5 py-4 font-medium">{{ $transaction->membershipLevel->name }}</td>
                                <td class="whitespace-nowrap px-5 py-4 font-medium">{{ $transaction->type }}</td>
                                <td class="whitespace-nowrap px-5 py-4"><span class="rounded-full bg-blue-50 px-2.5 py-1 text-xs font-bold text-blue-800">{{ $transaction->status }}</span></td>
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

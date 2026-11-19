<?php

namespace App\Console\Commands;

use App\Models\MembershipTransaction;
use App\TransactionStatus;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ExpireMembershipTransactions extends Command
{
    protected $signature = 'transactions:expire';

    protected $description = 'Expire overdue unpaid transactions and ended subscriptions';

    public function handle(): int
    {
        MembershipTransaction::query()
            ->where(function ($query): void {
                $query->where(function ($query): void {
                    $query->whereIn('status', [TransactionStatus::Pending->value, TransactionStatus::Paid->value])
                        ->where('created_at', '<=', now()->subDays(7));
                })->orWhere(function ($query): void {
                    $query->where('status', TransactionStatus::Approve->value)
                        ->where('expires_at', '<=', now());
                });
            })
            ->select('id')
            ->chunkById(100, function ($transactions): void {
                foreach ($transactions as $transaction) {
                    DB::transaction(function () use ($transaction): void {
                        $locked = MembershipTransaction::query()->lockForUpdate()->find($transaction->id);

                        if ($locked && (
                            (in_array($locked->status, [TransactionStatus::Pending->value, TransactionStatus::Paid->value], true)
                                && $locked->created_at->addDays(7)->lte(now()))
                            || ($locked->status === TransactionStatus::Approve->value
                                && $locked->expires_at?->lte(now()))
                        )) {
                            $locked->update(['status' => TransactionStatus::Expired->value]);
                        }
                    });
                }
            });

        return self::SUCCESS;
    }
}

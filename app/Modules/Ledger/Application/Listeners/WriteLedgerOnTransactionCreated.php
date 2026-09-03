<?php

namespace App\Modules\Ledger\Application\Listeners;

use App\Modules\Ledger\Infrastructure\Persistence\Eloquent\Models\LedgerEntryModel;
use App\Modules\Transaction\Domain\Events\TransactionCreated;

final class WriteLedgerOnTransactionCreated
{
    public function handle(TransactionCreated $event): void
    {
        if ($event->status === 'completed') {
            LedgerEntryModel::create([
                'account_id' => $event->accountId,
                'transaction_id' => $event->transactionId,
                'direction' => $event->direction,
                'amount' => abs($event->amount),
                'currency' => $event->currency,
            ]);
        }
    }
}

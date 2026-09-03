<?php

namespace App\Modules\Ledger\Application\Listeners;

use App\Modules\Ledger\Infrastructure\Persistence\Eloquent\Models\LedgerEntryModel;
use App\Modules\Transaction\Domain\Events\TransactionDeleted;

final class DeleteLedgerOnTransactionDeleted
{
    public function handle(TransactionDeleted $event): void
    {
        LedgerEntryModel::query()
            ->where('transaction_id', $event->transactionId)
            ->delete();
    }
}

<?php

namespace App\Modules\Ledger\Application\Listeners;

use App\Modules\Ledger\Infrastructure\Persistence\Eloquent\Models\LedgerEntryModel;
use App\Modules\Transaction\Domain\Events\TransactionUpdated;

final class SyncLedgerOnTransactionUpdated
{
    public function handle(TransactionUpdated $event): void
    {
        $ledgerEntries = LedgerEntryModel::query()
            ->where('transaction_id', $event->transactionId)
            ->get();

        if ($event->status === 'completed') {
            if ($ledgerEntries->isEmpty()) {
                // Newly completed transaction, create ledger entry
                LedgerEntryModel::create([
                    'account_id' => $event->accountId,
                    'transaction_id' => $event->transactionId,
                    'direction' => $event->direction,
                    'amount' => abs($event->amount),
                    'currency' => $event->currency,
                ]);
            } else {
                // Update related ledger entries if financial fields changed
                LedgerEntryModel::query()
                    ->where('transaction_id', $event->transactionId)
                    ->update([
                        'account_id' => $event->accountId,
                        'direction' => $event->direction,
                        'amount' => abs($event->amount),
                        'currency' => $event->currency,
                    ]);
            }
        } else {
            // If it is now pending or cancelled, remove ledger entries to revert balance
            LedgerEntryModel::query()
                ->where('transaction_id', $event->transactionId)
                ->delete();
        }
    }
}

<?php

namespace App\Modules\Account\Application\Listeners;

use App\Modules\Account\Infrastructure\Services\AccountBalanceService;
use App\Modules\Transaction\Domain\Events\TransactionCreated;
use App\Modules\Transaction\Domain\Events\TransactionDeleted;
use App\Modules\Transaction\Domain\Events\TransactionUpdated;
use Illuminate\Contracts\Queue\ShouldQueue;

/**
 * Listens for transaction events and recalculates the account balance cache.
 * Even though we run synchronously, we can implement ShouldQueue later if needed.
 */
final class SyncAccountBalanceOnTransaction
{
    public function __construct(
        private readonly AccountBalanceService $balanceService
    ) {}

    public function handleCreated(TransactionCreated $event): void
    {
        $this->balanceService->recalculateAndUpdate($event->accountId);
    }

    public function handleUpdated(TransactionUpdated $event): void
    {
        // For updates, the account could have changed (e.g. transfer correction),
        // or the amount could have changed. Just recalculating the current account is usually enough.
        // If the oldAccountId was available in the event, we would update both.
        // For safety, we update the one tied to the event.
        $this->balanceService->recalculateAndUpdate($event->accountId);
    }

    public function handleDeleted(TransactionDeleted $event): void
    {
        $this->balanceService->recalculateAndUpdate($event->accountId);
    }
}

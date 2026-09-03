<?php

namespace App\Modules\Ledger\Providers;

use App\Modules\Ledger\Application\Listeners\DeleteLedgerOnTransactionDeleted;
use App\Modules\Ledger\Application\Listeners\SyncLedgerOnTransactionUpdated;
use App\Modules\Ledger\Application\Listeners\WriteLedgerOnTransactionCreated;
use App\Modules\Transaction\Domain\Events\TransactionCreated;
use App\Modules\Transaction\Domain\Events\TransactionDeleted;
use App\Modules\Transaction\Domain\Events\TransactionUpdated;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

class LedgerServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        Event::listen(
            TransactionCreated::class,
            WriteLedgerOnTransactionCreated::class,
        );

        Event::listen(
            TransactionUpdated::class,
            SyncLedgerOnTransactionUpdated::class,
        );

        Event::listen(
            TransactionDeleted::class,
            DeleteLedgerOnTransactionDeleted::class,
        );
    }
}

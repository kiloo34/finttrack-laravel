<?php

namespace App\Modules\Ledger\Providers;

use Illuminate\Support\ServiceProvider;

class LedgerServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        \Illuminate\Support\Facades\Event::listen(
            \App\Modules\Transaction\Domain\Events\TransactionCreated::class,
            \App\Modules\Ledger\Application\Listeners\WriteLedgerOnTransactionCreated::class,
        );

        \Illuminate\Support\Facades\Event::listen(
            \App\Modules\Transaction\Domain\Events\TransactionUpdated::class,
            \App\Modules\Ledger\Application\Listeners\SyncLedgerOnTransactionUpdated::class,
        );

        \Illuminate\Support\Facades\Event::listen(
            \App\Modules\Transaction\Domain\Events\TransactionDeleted::class,
            \App\Modules\Ledger\Application\Listeners\DeleteLedgerOnTransactionDeleted::class,
        );
    }
}

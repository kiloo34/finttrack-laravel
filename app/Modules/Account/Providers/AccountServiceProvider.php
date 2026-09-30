<?php

namespace App\Modules\Account\Providers;

use App\Modules\Account\Domain\Repositories\AccountRepositoryInterface;
use App\Modules\Account\Domain\Repositories\InvestmentAssetRepositoryInterface;
use App\Modules\Account\Infrastructure\Persistence\Repositories\EloquentAccountRepository;
use App\Modules\Account\Infrastructure\Persistence\Repositories\EloquentInvestmentAssetRepository;
use Illuminate\Support\ServiceProvider;

class AccountServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Bind Domain Interface (Port) → Infrastructure Implementation (Adapter)
        $this->app->bind(
            AccountRepositoryInterface::class,
            EloquentAccountRepository::class,
        );
        $this->app->bind(
            InvestmentAssetRepositoryInterface::class,
            EloquentInvestmentAssetRepository::class,
        );
    }

    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__.'/../Presentation/Http/routes.php');

        \Illuminate\Support\Facades\Event::listen(
            \App\Modules\Transaction\Domain\Events\TransactionCreated::class,
            [\App\Modules\Account\Application\Listeners\SyncAccountBalanceOnTransaction::class, 'handleCreated']
        );
        \Illuminate\Support\Facades\Event::listen(
            \App\Modules\Transaction\Domain\Events\TransactionUpdated::class,
            [\App\Modules\Account\Application\Listeners\SyncAccountBalanceOnTransaction::class, 'handleUpdated']
        );
        \Illuminate\Support\Facades\Event::listen(
            \App\Modules\Transaction\Domain\Events\TransactionDeleted::class,
            [\App\Modules\Account\Application\Listeners\SyncAccountBalanceOnTransaction::class, 'handleDeleted']
        );
    }
}

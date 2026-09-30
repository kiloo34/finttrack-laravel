<?php

namespace App\Modules\Account\Providers;

use App\Modules\Account\Application\Listeners\SyncAccountBalanceOnTransaction;
use App\Modules\Account\Domain\Repositories\AccountRepositoryInterface;
use App\Modules\Account\Domain\Repositories\InvestmentAssetRepositoryInterface;
use App\Modules\Account\Infrastructure\Persistence\Repositories\EloquentAccountRepository;
use App\Modules\Account\Infrastructure\Persistence\Repositories\EloquentInvestmentAssetRepository;
use App\Modules\Transaction\Domain\Events\TransactionCreated;
use App\Modules\Transaction\Domain\Events\TransactionDeleted;
use App\Modules\Transaction\Domain\Events\TransactionUpdated;
use Illuminate\Support\Facades\Event;
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

        Event::listen(
            TransactionCreated::class,
            [SyncAccountBalanceOnTransaction::class, 'handleCreated']
        );
        Event::listen(
            TransactionUpdated::class,
            [SyncAccountBalanceOnTransaction::class, 'handleUpdated']
        );
        Event::listen(
            TransactionDeleted::class,
            [SyncAccountBalanceOnTransaction::class, 'handleDeleted']
        );
    }
}

<?php

namespace App\Modules\Transaction\Providers;

use App\Modules\Transaction\Domain\Repositories\RecurringTransactionRepositoryInterface;
use App\Modules\Transaction\Domain\Repositories\TransactionRepositoryInterface;
use App\Modules\Transaction\Domain\Services\HolidayServiceInterface;
use App\Modules\Transaction\Infrastructure\Persistence\Repositories\EloquentRecurringTransactionRepository;
use App\Modules\Transaction\Infrastructure\Persistence\Repositories\EloquentTransactionRepository;
use App\Modules\Transaction\Infrastructure\Services\IndonesianHolidayService;
use Illuminate\Support\ServiceProvider;

class TransactionServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(
            TransactionRepositoryInterface::class,
            EloquentTransactionRepository::class,
        );

        $this->app->bind(
            RecurringTransactionRepositoryInterface::class,
            EloquentRecurringTransactionRepository::class,
        );

        $this->app->singleton(
            HolidayServiceInterface::class,
            IndonesianHolidayService::class,
        );
    }

    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__.'/../Presentation/Http/routes.php');
    }
}

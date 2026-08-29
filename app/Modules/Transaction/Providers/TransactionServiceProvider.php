<?php

namespace App\Modules\Transaction\Providers;

use App\Modules\Transaction\Domain\Repositories\TransactionRepositoryInterface;
use App\Modules\Transaction\Infrastructure\Persistence\Repositories\EloquentTransactionRepository;
use Illuminate\Support\ServiceProvider;

class TransactionServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(
            TransactionRepositoryInterface::class,
            EloquentTransactionRepository::class,
        );
    }

    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__.'/../Presentation/Http/routes.php');
    }
}

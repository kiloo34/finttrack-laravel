<?php

namespace App\Modules\Transfer\Providers;

use App\Modules\Transfer\Domain\Repositories\TransferRepositoryInterface;
use App\Modules\Transfer\Infrastructure\Persistence\Repositories\EloquentTransferRepository;
use Illuminate\Support\ServiceProvider;

class TransferServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(
            TransferRepositoryInterface::class,
            EloquentTransferRepository::class,
        );
    }

    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__.'/../Presentation/Http/routes.php');
    }
}

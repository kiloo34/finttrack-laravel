<?php

namespace App\Modules\Account\Providers;

use App\Modules\Account\Domain\Repositories\AccountRepositoryInterface;
use App\Modules\Account\Infrastructure\Persistence\Repositories\EloquentAccountRepository;
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
    }

    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__.'/../Presentation/Http/routes.php');
    }
}

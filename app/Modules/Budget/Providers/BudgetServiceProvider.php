<?php

namespace App\Modules\Budget\Providers;

use App\Modules\Budget\Domain\Repositories\BudgetRepositoryInterface;
use App\Modules\Budget\Infrastructure\Persistence\Eloquent\Repositories\EloquentBudgetRepository;
use Illuminate\Support\ServiceProvider;

class BudgetServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(
            BudgetRepositoryInterface::class,
            EloquentBudgetRepository::class,
        );
    }

    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__.'/../Presentation/Http/routes.php');
    }
}


<?php

namespace App\Modules\Category\Providers;

use App\Modules\Category\Domain\Repositories\CategoryRepositoryInterface;
use App\Modules\Category\Infrastructure\Persistence\Repositories\EloquentCategoryRepository;
use Illuminate\Support\ServiceProvider;

class CategoryServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(
            CategoryRepositoryInterface::class,
            EloquentCategoryRepository::class,
        );
    }

    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__.'/../Presentation/Http/routes.php');
    }
}

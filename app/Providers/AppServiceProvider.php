<?php

namespace App\Providers;

use App\Modules\Account\Providers\AccountServiceProvider;
use App\Modules\Audit\Providers\AuditServiceProvider;
use App\Modules\Category\Providers\CategoryServiceProvider;
use App\Modules\Ledger\Providers\LedgerServiceProvider;
use App\Modules\Transaction\Providers\TransactionServiceProvider;
use App\Modules\Transfer\Providers\TransferServiceProvider;
use App\Modules\User\Providers\UserServiceProvider;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Register all Modular Monolith module providers
        $this->app->register(UserServiceProvider::class);
        $this->app->register(LedgerServiceProvider::class);
        $this->app->register(AuditServiceProvider::class);
        $this->app->register(AccountServiceProvider::class);
        $this->app->register(CategoryServiceProvider::class);
        $this->app->register(TransactionServiceProvider::class);
        $this->app->register(TransferServiceProvider::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::define('viewPulse', function (User $user) {
        // Ganti dengan email admin Anda
            return $user->email === 'robileksono@gmail.com';
        });
        $this->configureDefaults();
    }

    /**
     * Configure default behaviors for production-ready applications.
     */
    protected function configureDefaults(): void
    {
        Date::use(CarbonImmutable::class);

        DB::prohibitDestructiveCommands(
            app()->isProduction(),
        );

        Password::defaults(fn (): ?Password => app()->isProduction()
            ? Password::min(12)
                ->mixedCase()
                ->letters()
                ->numbers()
                ->symbols()
                ->uncompromised()
            : null,
        );
    }
}

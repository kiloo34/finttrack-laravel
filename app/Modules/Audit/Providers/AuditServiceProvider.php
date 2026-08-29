<?php

namespace App\Modules\Audit\Providers;

use Illuminate\Support\ServiceProvider;

class AuditServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // AuditService is stateless; no special binding needed.
        // Laravel auto-resolves it via the container.
    }

    public function boot(): void {}
}

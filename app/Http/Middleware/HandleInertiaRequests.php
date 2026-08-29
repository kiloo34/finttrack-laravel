<?php

namespace App\Http\Middleware;

use App\Modules\Account\Infrastructure\Persistence\Eloquent\Models\AccountTypeModel;
use App\Modules\Account\Infrastructure\Persistence\Eloquent\Models\CurrencyModel;
use App\Modules\Category\Infrastructure\Persistence\Eloquent\Models\CategoryTypeModel;
use App\Modules\Transaction\Infrastructure\Persistence\Eloquent\Models\TransactionTypeModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        return [
            ...parent::share($request),
            'name' => config('app.name'),
            'auth' => [
                'user' => $request->user(),
            ],
            'sidebarOpen' => ! $request->hasCookie('sidebar_state') || $request->cookie('sidebar_state') === 'true',
            'constants' => Cache::remember('system_constants_v4', 3600, function () {
                try {
                    return [
                        'ACCOUNT_TYPES' => AccountTypeModel::all()->values()->toArray(),
                        'CATEGORY_TYPES' => CategoryTypeModel::all()->values()->toArray(),
                        'CURRENCIES' => CurrencyModel::all()->values()->toArray(),
                        'TRANSACTION_TYPES' => TransactionTypeModel::all()->values()->toArray(),
                        'PROVIDERS' => \App\Modules\Account\Infrastructure\Persistence\Eloquent\Models\ProviderModel::all()->values()->toArray(),
                    ];
                } catch (\Exception $e) {
                    Log::error('Constants Cache Error: '.$e->getMessage());

                    return [
                        'ACCOUNT_TYPES' => [],
                        'CATEGORY_TYPES' => [],
                        'CURRENCIES' => [],
                        'TRANSACTION_TYPES' => [],
                        'PROVIDERS' => [],
                    ];
                }
            }),
        ];
    }
}

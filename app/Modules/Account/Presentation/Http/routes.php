<?php

use App\Modules\Account\Presentation\Http\Controllers\AccountController;
use App\Modules\Account\Presentation\Http\Controllers\InvestmentAssetController;
use Illuminate\Support\Facades\Route;

Route::prefix('api')->middleware(['api', 'auth:sanctum'])->as('api.')->group(function () {
    Route::apiResource('accounts', AccountController::class);
    Route::post('accounts/{account}/sync-balance', [AccountController::class, 'syncBalance'])->name('accounts.syncBalance');
    Route::get('accounts/{account}/assets', [InvestmentAssetController::class, 'index'])->name('accounts.assets.index');
    Route::post('accounts/{account}/assets', [InvestmentAssetController::class, 'store'])->name('accounts.assets.store');
    Route::put('accounts/{account}/assets/sync', [InvestmentAssetController::class, 'syncPrices'])->name('accounts.assets.sync');
});

<?php

use App\Modules\Account\Presentation\Http\Controllers\AccountController;
use Illuminate\Support\Facades\Route;

Route::prefix('api')->middleware(['api', 'auth:sanctum'])->as('api.')->group(function () {
    Route::apiResource('accounts', AccountController::class);
});

<?php

use App\Modules\Transaction\Presentation\Http\Controllers\TransactionController;
use Illuminate\Support\Facades\Route;

Route::prefix('api')->middleware(['api', 'auth:sanctum'])->group(function () {
    Route::apiResource('transactions', TransactionController::class);
});

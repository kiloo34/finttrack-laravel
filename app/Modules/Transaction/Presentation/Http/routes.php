<?php

use App\Modules\Transaction\Presentation\Http\Controllers\RecurringTransactionController;
use App\Modules\Transaction\Presentation\Http\Controllers\TransactionController;
use Illuminate\Support\Facades\Route;

Route::prefix('api')->middleware(['api', 'auth:sanctum'])->as('api.')->group(function () {
    Route::apiResource('transactions', TransactionController::class);

    // Recurring Transactions API
    Route::post('recurring-transactions/{id}/execute', [RecurringTransactionController::class, 'execute'])->name('recurring-transactions.execute');
    Route::apiResource('recurring-transactions', RecurringTransactionController::class);
});

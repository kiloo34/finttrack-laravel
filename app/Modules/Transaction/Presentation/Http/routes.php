<?php

use App\Modules\Transaction\Presentation\Http\Controllers\RecurringTransactionController;
use App\Modules\Transaction\Presentation\Http\Controllers\ReportController;
use App\Modules\Transaction\Presentation\Http\Controllers\TransactionController;
use Illuminate\Support\Facades\Route;

Route::prefix('api')->middleware(['api', 'auth:sanctum'])->as('api.')->group(function () {
    Route::get('transactions/summary', [TransactionController::class, 'summary'])->name('transactions.summary');
    Route::post('transactions/{id}/attachment', [TransactionController::class, 'uploadAttachment'])->name('transactions.attachment');
    Route::apiResource('transactions', TransactionController::class);

    // Reports API
    Route::get('reports/category-breakdown', [ReportController::class, 'categoryBreakdown'])->name('reports.category-breakdown');
    Route::get('reports/cashflow-trend', [ReportController::class, 'cashflowTrend'])->name('reports.cashflow-trend');

    // Recurring Transactions API
    Route::post('recurring-transactions/{id}/execute', [RecurringTransactionController::class, 'execute'])->name('recurring-transactions.execute');
    Route::apiResource('recurring-transactions', RecurringTransactionController::class);
});

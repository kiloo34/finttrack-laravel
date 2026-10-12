<?php

use App\Modules\Budget\Presentation\Http\Controllers\BudgetController;
use Illuminate\Support\Facades\Route;

Route::prefix('api')->middleware(['api', 'auth:sanctum'])->as('api.')->group(function () {
    Route::apiResource('budgets', BudgetController::class);
});


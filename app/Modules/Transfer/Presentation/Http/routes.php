<?php

use App\Modules\Transfer\Presentation\Http\Controllers\TransferController;
use Illuminate\Support\Facades\Route;

Route::prefix('api')->middleware(['api', 'auth:sanctum'])->as('api.')->group(function () {
    Route::apiResource('transfers', TransferController::class)->only(['index', 'store', 'show']);
});

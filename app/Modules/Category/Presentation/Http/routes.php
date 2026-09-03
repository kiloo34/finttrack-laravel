<?php

use App\Modules\Category\Presentation\Http\Controllers\CategoryController;
use Illuminate\Support\Facades\Route;

Route::prefix('api')->middleware(['api', 'auth:sanctum'])->as('api.')->group(function () {
    Route::apiResource('categories', CategoryController::class);
});

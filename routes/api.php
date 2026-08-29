<?php

/**
 * API Routes
 *
 * Module-specific routes are loaded by each Module's ServiceProvider
 * (AccountServiceProvider, CategoryServiceProvider, etc.).
 *
 * Only shared/global routes are defined here.
 */

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
});

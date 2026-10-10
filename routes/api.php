<?php

/**
 * API Routes
 *
 * Module-specific routes are loaded by each Module's ServiceProvider
 * (AccountServiceProvider, CategoryServiceProvider, etc.).
 *
 * Only shared/global routes are defined here.
 */

use App\Http\Controllers\NotificationController;
use App\Modules\User\Presentation\Http\Controllers\AuthApiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function () {
    Route::post('/login', [AuthApiController::class, 'login'])->name('api.auth.login');

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [AuthApiController::class, 'logout'])->name('api.auth.logout');
        Route::get('/me', [AuthApiController::class, 'me'])->name('api.auth.me');
    });
});

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // Notifications
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.api.index');
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllAsRead'])->name('notifications.api.read-all');
    Route::post('/notifications/{id}/read', [NotificationController::class, 'markAsRead'])->name('notifications.api.read');
});

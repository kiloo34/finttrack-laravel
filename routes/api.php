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
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // Notifications
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.api.index');
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllAsRead'])->name('notifications.api.read-all');
    Route::post('/notifications/{id}/read', [NotificationController::class, 'markAsRead'])->name('notifications.api.read');
});

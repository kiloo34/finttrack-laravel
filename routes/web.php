<?php

use App\Http\Controllers\ConstantsController;
use Illuminate\Support\Facades\Route;

Route::inertia('/', 'welcome')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::inertia('dashboard', 'dashboard')->name('dashboard');
    Route::inertia('transactions', 'transactions/index')->name('transactions.index');
    Route::inertia('transactions/create', 'transactions/create')->name('transactions.create');
    Route::inertia('transactions/{id}/edit', 'transactions/edit')->name('transactions.edit');
    Route::inertia('accounts', 'accounts/index')->name('accounts.index');
    Route::inertia('categories', 'categories/index')->name('categories.index');
    Route::inertia('recurring-transactions', 'recurring-transactions/index')->name('recurring-transactions.index');
    Route::get('/constants', [ConstantsController::class, 'index'])->name('constants.index');

});

require __DIR__.'/settings.php';

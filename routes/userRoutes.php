<?php


use App\Http\Controllers\DashboardController;
use App\Http\Controllers\User\AuthController;

Route::middleware(['auth'])->group(function() {
    Route::get('/dashboard', [DashboardController::class, 'doDefault'])
        ->name('dashboard');

    Route::match(['get', 'post'], '/logout', [AuthController::class, 'logout'])
        ->name('logout');

});

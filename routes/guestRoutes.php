<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\User\AuthController;
use App\Http\Controllers\User\PasswordResetController;

Route::get('/', [HomeController::class, 'doDefault'])->name('home');

Route::middleware('guest')->group(function () {
    Route::match(['get', 'post'], '/register', [AuthController::class, 'register'])->name('register');
    Route::match(['get', 'post'], '/login', [AuthController::class, 'login'])->name('login');

    Route::get('/forgot-password', [PasswordResetController::class, 'forgot'])->name('password.request');
    Route::post('/forgot-password', [PasswordResetController::class, 'forgot'])->name('password.email');
    Route::get('/reset-password/{token}', [PasswordResetController::class, 'reset'])->name('password.reset');
    Route::post('/reset-password', [PasswordResetController::class, 'reset'])->name('password.update');
});

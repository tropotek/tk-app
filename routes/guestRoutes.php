<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\User\AuthController;


Route::get('/', [HomeController::class, 'doDefault'])->name('home');

Route::middleware('guest')->group(function () {
    Route::match(['get', 'post'], '/register', [AuthController::class, 'register'])->name('register');
    Route::match(['get', 'post'], '/login', [AuthController::class, 'login'])->name('login');
});



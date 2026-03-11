<?php

// TODO: admin permissions
use App\Http\Controllers\User\StaffController;
use App\Http\Controllers\User\UserController;

Route::middleware('auth')->group(function () {


    // Manage Users
    Route::get('/users', [UserController::class, 'index']);
    Route::get('/user/create', [UserController::class, 'create']);
    Route::get('/user/{user}', [UserController::class, 'show']);
    Route::get('/user/{user}/edit', [UserController::class, 'edit']);
    Route::patch('/user/{user}', [UserController::class, 'update']);
    Route::post('/user', [UserController::class, 'store']);

    // Manage Staff
    Route::get('/staff', [StaffController::class, 'index']);
    Route::get('/staff/create', [StaffController::class, 'create']);
    Route::get('/staff/{staff}', [StaffController::class, 'show']);
    Route::get('/staff/{staff}/edit', [StaffController::class, 'edit']);
    Route::patch('/staff/{staff}', [StaffController::class, 'update']);
    Route::post('/staff', [StaffController::class, 'store']);


});

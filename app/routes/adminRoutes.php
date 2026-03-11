<?php

// TODO: admin permissions
use App\Http\Controllers\User\UserController;

Route::middleware('auth')->group(function () {


    // Manage Users
    Route::get('/users', [UserController::class, 'index']);
    Route::get('/user/create', [UserController::class, 'create']);
    Route::get('/user/{user}', [UserController::class, 'show']);
    Route::get('/user/{user}/edit', [UserController::class, 'edit']);
    Route::patch('/user/{user}', [UserController::class, 'update']);
    Route::post('/user', [UserController::class, 'store']);


});

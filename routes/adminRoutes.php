<?php

// TODO: admin permissions
use App\Http\Controllers\User\UserController;

Route::middleware(['auth', 'role:admin'])->name('admin.')->group(function () {


    // Manage Users
    Route::name('users.')->group(function () {

        Route::livewire('/users2', 'pages::users')->name('index2');

        Route::get('/users', [UserController::class, 'index'])->name('index');
        Route::get('/user/create', [UserController::class, 'create'])->name('create');
        Route::get('/user/{user}', [UserController::class, 'show'])->name('show');
        Route::get('/user/{user}/edit', [UserController::class, 'edit'])->name('edit');
        Route::patch('/user/{user}', [UserController::class, 'update'])->name('update');
        Route::post('/user', [UserController::class, 'store'])->name('store');
    });


    // development-specific
    if (app()->environment('local')) {
        Route::get('/phpinfo', fn() => phpinfo())->name('phpinfo');
        Route::get('/user',
            fn() => '<pre>'.print_r(Auth::user()->attributesToArray(), true).'</pre>')->name('dump-user');
        Route::get('/session', fn() => '<pre>'.print_r(session()->all(), true).'</pre>')->name('dump-session');
    }
});

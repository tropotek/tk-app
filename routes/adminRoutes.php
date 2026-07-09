<?php

Route::middleware(['auth', 'can:accessAdmin'])->prefix('admin')->name('admin.')->group(function () {

    // Manage Users
    Route::name('users.')->group(function () {
        Route::livewire('/users', 'pages::users')->name('index');
        Route::livewire('/user/create', 'pages::users.edit')->name('create');
        Route::livewire('/user/{user}/edit', 'pages::users.edit')->name('edit');
    });

    // development-specific
    Route::get('/phpinfo', fn () => phpinfo())->name('phpinfo');
    Route::get('/user',
        fn () => '<pre>'.print_r(Auth::user()->attributesToArray(), true).'</pre>')->name('dump-user');
    Route::get('/session', fn () => '<pre>'.print_r(session()->all(), true).'</pre>')->name('dump-session');

});

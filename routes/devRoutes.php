<?php

use Illuminate\Support\Facades\Route;

if (app()->environment('local')) {
    // development-specific
    Route::get('/phpinfo', fn() => phpinfo());
    Route::get('/user', fn() => '<pre>' . print_r(Auth::user()->attributesToArray(), true) . '</pre>');
    Route::get('/session', fn() => '<pre>' . print_r(session()->all(), true) . '</pre>');
}

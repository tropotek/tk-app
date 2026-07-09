<?php

Route::livewire('/', 'pages::home')->name('home');

Route::middleware('guest')->group(function () {
    Route::livewire('/register', 'pages::register')->name('register');
    Route::livewire('/login', 'pages::login')->name('login');

    Route::livewire('/forgot-password', 'pages::forgot-password')->name('password.request');
    Route::livewire('/reset-password/{token}', 'pages::reset-password')->name('password.reset');
});

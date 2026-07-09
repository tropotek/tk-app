<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\User\AuthController;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Tk\Models\File;

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'doDefault'])
        ->name('dashboard');

    Route::livewire('/myprofile', 'pages::myprofile')->name('myprofile');

    Route::match(['get', 'post'], '/logout', [AuthController::class, 'logout'])
        ->name('logout');

    Route::get('/files/{file}', function (File $file) {
        abort_unless(
            $file->fkey === User::class && $file->fid === auth()->id(),
            403
        );

        return Storage::disk('local')->response($file->path, $file->original_name);
    })->name('files.view');

});

<?php


use App\Http\Controllers\DashboardController;
use App\Http\Controllers\User\AuthController;
use App\Models\File;
use Illuminate\Support\Facades\Storage;

Route::middleware(['auth'])->group(function() {
    Route::get('/dashboard', [DashboardController::class, 'doDefault'])
        ->name('dashboard');

    Route::match(['get', 'post'], '/logout', [AuthController::class, 'logout'])
        ->name('logout');

    Route::get('/files/{file}', function (File $file) {
        abort_unless(
            $file->fkey === App\Models\User::class && $file->fid === auth()->id(),
            403
        );
        return Storage::disk('local')->response($file->path, $file->original_name);
    })->name('files.view');

});

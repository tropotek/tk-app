<?php

use App\Http\Controllers\Examples\ArrayTableController;
use App\Http\Controllers\Examples\ExamplesController;
use App\Http\Controllers\Examples\Forms\FieldsetController;
use App\Http\Controllers\Examples\Forms\OneController;
use App\Http\Controllers\Examples\Forms\ThreeController;
use App\Http\Controllers\Examples\Forms\TwoController;
use App\Http\Controllers\Examples\IdeaController;


// Example pages
Route::get('/examples/examples', [ExamplesController::class, 'index']);

// Laracasts Ideas tutorial
Route::middleware('auth')->name('examples.')->prefix('/examples')->group(function () {

    Route::name('ideas.')->group(function () {
        Route::livewire('/ideas', 'pages::examples.ideas')->name('index');
        Route::get('/ideas/create', [IdeaController::class, 'create'])->name('create');
        Route::get('/ideas/{idea}', [IdeaController::class, 'show'])->name('show');
        Route::get('/ideas/{idea}/edit', [IdeaController::class, 'edit'])->name('edit');
        Route::patch('/ideas/{idea}', [IdeaController::class, 'update'])->name('update');
        Route::post('/ideas', [IdeaController::class, 'store'])->name('index');
        Route::delete('/ideas/{idea}', [IdeaController::class, 'destroy'])->name('destroy');
        Route::get('/delete-all', [IdeaController::class, 'deleteAll'])->name('deleteAll');
    });
});

// Form Examples
Route::get('/examples/formOne', [OneController::class, 'index']);
Route::get('/examples/formOne/edit', [OneController::class, 'edit']);
Route::get('/examples/formOne/create', [OneController::class, 'create']);
Route::post('/examples/formOne/submit', [OneController::class, 'submit']);

Route::get('/examples/formTwo', [TwoController::class, 'index']);
Route::get('/examples/formTwo/edit', [TwoController::class, 'edit']);
Route::get('/examples/formTwo/create', [TwoController::class, 'create']);
Route::post('/examples/formTwo/submit', [TwoController::class, 'submit']);

Route::get('/examples/formThree', [ThreeController::class, 'index']);
Route::get('/examples/formThree/edit', [ThreeController::class, 'edit']);
Route::get('/examples/formThree/create', [ThreeController::class, 'create']);
Route::post('/examples/formThree/submit', [ThreeController::class, 'submit']);

Route::get('/examples/formFieldset', [FieldsetController::class, 'index']);
Route::get('/examples/formFieldset/edit', [FieldsetController::class, 'edit']);
Route::get('/examples/formFieldset/create', [FieldsetController::class, 'create']);
Route::post('/examples/formFieldset/submit', [FieldsetController::class, 'submit']);

// Table Examples
Route::get('/examples/tableArray', [ArrayTableController::class, 'index']);
Route::livewire('/examples/tableArray2', 'pages::examples.tables.table-array-live');


<?php

use App\Http\Controllers\Examples\ArrayTableController;
use App\Http\Controllers\Examples\ExamplesController;
use App\Http\Controllers\Examples\Forms\FieldsetController;
use App\Http\Controllers\Examples\Forms\FieldgroupController;
use App\Http\Controllers\Examples\IdeaController;

// Laracasts Ideas tutorial
Route::middleware('auth')->name('examples.')->prefix('/examples')->group(function () {
    Route::get('/examples', [ExamplesController::class, 'index'])->name('index');

    Route::name('ideas.')->group(function () {
        Route::livewire('/ideas', 'pages::examples.ideas')->name('index');
        Route::get('/ideas/create', [IdeaController::class, 'create'])->name('create');
        Route::get('/ideas/{idea}', [IdeaController::class, 'show'])->name('show');
        Route::get('/ideas/{idea}/edit', [IdeaController::class, 'edit'])->name('edit');
        Route::patch('/ideas/{idea}', [IdeaController::class, 'update'])->name('update');
        Route::post('/ideas', [IdeaController::class, 'store'])->name('stor');
        Route::delete('/ideas/{idea}', [IdeaController::class, 'destroy'])->name('destroy');
        Route::get('/ideas/delete-all', [IdeaController::class, 'deleteAll'])->name('deleteAll');
    });

    // Form Examples
    Route::get('/formThree', [FieldgroupController::class, 'index'])->name('formThree');
    Route::get('/formThree/edit', [FieldgroupController::class, 'edit'])->name('formThree.edit');
    Route::get('/formThree/create', [FieldgroupController::class, 'create'])->name('formThree.create');
    Route::post('/formThree/submit', [FieldgroupController::class, 'submit'])->name('formThree.submit');

    Route::get('/formFieldset', [FieldsetController::class, 'index'])->name('formFieldset');
    Route::get('/formFieldset/edit', [FieldsetController::class, 'edit'])->name('formFieldset.edit');
    Route::get('/formFieldset/create', [FieldsetController::class, 'create'])->name('formFieldset.create');
    Route::post('/formFieldset/submit', [FieldsetController::class, 'submit'])->name('formFieldset.submit');

    // Table Examples
    Route::get('/tableArray', [ArrayTableController::class, 'index'])->name('tableArray');
    Route::livewire('/tableArray2', 'pages::examples.tables.table-array-live')->name('tableArray2');

});

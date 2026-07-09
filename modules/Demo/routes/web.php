<?php

use Demo\Http\Controllers\ArrayTableController;
use Demo\Http\Controllers\ExamplesController;
use Demo\Http\Controllers\Forms\FieldgroupController;
use Demo\Http\Controllers\Forms\FieldsetController;
use Demo\Http\Controllers\IdeaController;

// Laracasts Ideas tutorial
Route::middleware(['web', 'auth'])->name('examples.')->prefix('/examples')->group(function () {
    Route::get('/examples', [ExamplesController::class, 'index'])->name('index');
    Route::livewire('/bootstrap', 'demo::examples.bootstrap')->name('bootstrap');

    Route::name('ideas.')->group(function () {
        Route::livewire('/ideas', 'demo::examples.ideas')->name('index');
        Route::get('/ideas/create', [IdeaController::class, 'create'])->name('create');
        Route::get('/ideas/{idea}', [IdeaController::class, 'show'])->name('show');
        Route::get('/ideas/{idea}/edit', [IdeaController::class, 'edit'])->name('edit');
        Route::patch('/ideas/{idea}', [IdeaController::class, 'update'])->name('update');
        Route::post('/ideas', [IdeaController::class, 'store'])->name('store');
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
    Route::get('/tableArrayExport', [ArrayTableController::class, 'export'])->name('tableArray.export');

    Route::livewire('/tableArray2', 'demo::examples.tables.table-array-live')->name('tableArray2');

    Route::livewire('/tableTest', 'demo::examples.tables.test')->name('tableTest');

});

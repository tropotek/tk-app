<?php

use App\Http\Controllers\Examples\ExamplesController;
use App\Http\Controllers\Examples\Forms\FieldsetController;
use App\Http\Controllers\Examples\Forms\OneController;
use App\Http\Controllers\Examples\Forms\ThreeController;
use App\Http\Controllers\Examples\Forms\TwoController;
use App\Http\Controllers\Examples\Ideas\IdeaController;
use App\Http\Controllers\Examples\Tables\ArrayTable;
use App\Http\Controllers\Examples\Tables\CsvTable;
use App\Http\Controllers\Examples\Tables\LivewireTable;
use App\Http\Controllers\Examples\Tables\QueryTable;


// Example pages
Route::get('/examples', [ExamplesController::class, 'index']);

// Laracasts Ideas tutorial
Route::middleware('auth')->group(function () {
    Route::get('/ideas', [IdeaController::class, 'index']);
    Route::get('/ideas/create', [IdeaController::class, 'create']);
    Route::get('/ideas/{idea}', [IdeaController::class, 'show']);
    Route::get('/ideas/{idea}/edit', [IdeaController::class, 'edit']);
    Route::patch('/ideas/{idea}', [IdeaController::class, 'update']);
    Route::post('/ideas', [IdeaController::class, 'store']);
    Route::delete('/ideas/{idea}', [IdeaController::class, 'destroy']);
    Route::get('/delete-all', [IdeaController::class, 'deleteAll']);
});

// Form Test Examples
Route::get('/formOne', [OneController::class, 'index']);
Route::get('/formOne/edit', [OneController::class, 'edit']);
Route::get('/formOne/create', [OneController::class, 'create']);
Route::post('/formOne/submit', [OneController::class, 'submit']);

Route::get('/formTwo', [TwoController::class, 'index']);
Route::get('/formTwo/edit', [TwoController::class, 'edit']);
Route::get('/formTwo/create', [TwoController::class, 'create']);
Route::post('/formTwo/submit', [TwoController::class, 'submit']);

Route::get('/formThree', [ThreeController::class, 'index']);
Route::get('/formThree/edit', [ThreeController::class, 'edit']);
Route::get('/formThree/create', [ThreeController::class, 'create']);
Route::post('/formThree/submit', [ThreeController::class, 'submit']);

Route::get('/formFieldset', [FieldsetController::class, 'index']);
Route::get('/formFieldset/edit', [FieldsetController::class, 'edit']);
Route::get('/formFieldset/create', [FieldsetController::class, 'create']);
Route::post('/formFieldset/submit', [FieldsetController::class, 'submit']);

// Table Test Examples
Route::get('/tableLivewire', [LivewireTable::class, 'index']);
Route::get('/tableQuery', [QueryTable::class, 'index']);
Route::get('/tableArray', [ArrayTable::class, 'index']);
Route::get('/tableCsv', [CsvTable::class, 'index']);


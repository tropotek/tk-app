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
Route::get('/examples/examples', [ExamplesController::class, 'index']);

// Laracasts Ideas tutorial
Route::middleware('auth')->group(function () {
    Route::get('/examples/ideas', [IdeaController::class, 'index']);
    Route::get('/examples/ideas/create', [IdeaController::class, 'create']);
    Route::get('/examples/ideas/{idea}', [IdeaController::class, 'show']);
    Route::get('/examples/ideas/{idea}/edit', [IdeaController::class, 'edit']);
    Route::patch('/examples/ideas/{idea}', [IdeaController::class, 'update']);
    Route::post('/examples/ideas', [IdeaController::class, 'store']);
    Route::delete('/examples/ideas/{idea}', [IdeaController::class, 'destroy']);
    Route::get('/examples/delete-all', [IdeaController::class, 'deleteAll']);
});

// Form Test Examples
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

// Table Test Examples
Route::get('/examples/tableLivewire', [LivewireTable::class, 'index']);
Route::get('/examples/tableLivewireTwo', [\App\Http\Controllers\Examples\Tables\LivewireTwoTable::class, 'index']);
Route::get('/examples/tableQuery', [QueryTable::class, 'index']);
Route::get('/examples/tableArray', [ArrayTable::class, 'index']);
Route::get('/examples/tableCsv', [CsvTable::class, 'index']);


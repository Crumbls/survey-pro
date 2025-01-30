<?php

use Illuminate\Support\Facades\Route;

Route::get('/', [\App\Http\Controllers\ReportController::class, 'show'])->name('show');


Route::get('edit', \App\Livewire\Report\EditResource::class)
    ->name('edit');


/*

Route::resource('reports', \App\Http\Controllers\ReportController::class)
    ->except(['index', 'create', 'edit', 'update', 'show'])
    ->parameters([
        'report' => 'record'
    ]);
*/

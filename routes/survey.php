<?php

use Illuminate\Support\Facades\Route;


/**
 * Wrong action.
 */
Route::get('/', [\App\Http\Controllers\SurveyController::class, 'show'])
    ->name('show');

Route::get('/edit', [\App\Http\Controllers\SurveyController::class, 'edit'])
    ->name('edit');

Route::patch('/update', [\App\Http\Controllers\SurveyController::class, 'update'])
    ->name('update');



Route::get('collectors', \App\Livewire\Collector\ListResource::class)
    ->name('collectors.index');

Route::get('collectors/create', \App\Livewire\Collector\CreateResource::class)
    ->name('collectors.create');


Route::get('responses', \App\Livewire\Response\ListResource::class)
    ->name('responses.index');

Route::get('reports', \App\Livewire\Report\ListResource::class)
    ->name('reports.index');

Route::get('reports/create', \App\Livewire\Report\CreateResource::class)
    ->name('reports.create');

Route::get('/create', \App\Livewire\Survey\CreateResource::class)
    ->name('create');

/*
Route::resource('surveys', \App\Http\Controllers\SurveyController::class)
    ->except(['index'])
    ->parameters([
        'survey' => 'record'
    ]);

*/

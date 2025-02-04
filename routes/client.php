<?php

use Illuminate\Support\Facades\Route;
/*
Route::get('/', \App\Http\Controllers\TenantController::class)
    ->name('show');
*/

Route::get('/', \App\Livewire\Client\ShowResource::class)
    ->name('show');

Route::get('/surveys', \App\Livewire\Survey\ListResource::class)
    ->name('surveys.index');
Route::get('/surveys/create', \App\Livewire\Survey\CreateResource::class)
    ->name('surveys.create');


Route::get('/edit', \App\Http\Controllers\ClientController::class)->name('edit');

Route::get('/collectors', \App\Livewire\Collector\ListResource::class)->name('collectors.index');
Route::get('/collectors/create', \App\Livewire\Collector\CreateResource::class)->name('collectors.create');

Route::get('/responses', \App\Livewire\Response\ListResource::class)
    ->name('responses.index');

Route::get('reports', \App\Livewire\Report\ListResource::class)
    ->name('reports.index');

Route::get('reports/create', \App\Livewire\Report\CreateResource::class)
    ->name('reports.create');

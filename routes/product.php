<?php

use Illuminate\Support\Facades\Route;
/*
Route::get('/', \App\Http\Controllers\TenantController::class)
    ->name('show');
*/

Route::get('/', \App\Livewire\Product\ShowResource::class)->name('show');
Route::get('/edit', \App\Livewire\Product\EditResource::class)->name('edit');

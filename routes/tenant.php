<?php

use Illuminate\Support\Facades\Route;

Route::get('/', \App\Http\Controllers\TenantController::class)
    ->name('show');

Route::get('/edit', function() {
    return view('tenant.edit', ['record' => request()->tenant,
        'breadcrumbs' => []]);
    dd('rewrite this.');
})->name('edit');

Route::get('clients', \App\Livewire\Client\ListResource::class)
    ->name('clients.index');

Route::get('clients/create', \App\Livewire\Client\CreateResource::class)
    ->name('clients.create');


Route::get('collectors', \App\Livewire\Collector\ListResource::class)
    ->name('collectors.index');

Route::get('collectors/create', \App\Livewire\Collector\CreateResource::class)
    ->name('collectors.create');

Route::get('products', \App\Livewire\Product\ListResource::class)
    ->name('products.index');

Route::get('products/create', \App\Livewire\Product\CreateResource::class)
    ->name('products.create');

Route::get('reports', \App\Livewire\Report\ListResource::class)
    ->name('reports.index');

Route::get('reports/create', \App\Livewire\Report\CreateResource::class)
    ->name('reports.create');

Route::get('surveys', \App\Livewire\Survey\ListResource::class)->name('surveys.index');

Route::get('surveys/create', \App\Livewire\Survey\CreateResource::class)
    ->name('surveys.create');

Route::get('users', \App\Livewire\User\ListResource::class)
    ->name('users.index');

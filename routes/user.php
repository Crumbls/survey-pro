<?php

use Illuminate\Support\Facades\Route;

Route::middleware(['web', 'auth'])->group(function () {
    Route::get('grapesjs/editor', [\App\Http\Controllers\GrapesJsController::class, 'editor'])
        ->name('grapesjs.editor');
    Route::post('grapesjs/upload', [\App\Http\Controllers\GrapesJsController::class, 'upload'])
        ->name('grapesjs.upload');
});

Route::group([
    'middleware' => ['auth']
], function() {
    Route::get('/dashboard', \App\Http\Controllers\DashboardController::class)
        ->name('dashboard');

    Route::get('/notifications', \App\Livewire\Notification\ListResource::class)
        ->name('notifications.index');

    Route::middleware('auth')->group(function () {
        Route::get('/profile', [\App\Http\Controllers\ProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/profile', [\App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');
        Route::delete('/profile', [\App\Http\Controllers\ProfileController::class, 'destroy'])->name('profile.destroy');
    });
});

Route::group([
    'middleware' => ['auth', 'verified']
], function() {
    /*
    Route::get('surveys/{surveyId}/collectors/create', [\App\Http\Controllers\CollectorController::class, 'create'])
        ->name('surveys.collectors.create');
*/


    Route::get('collectors/create', \App\Livewire\Collector\CreateResource::class)
        ->name('collectors.create');

    Route::get('collectors', \App\Livewire\Collector\ListResource::class)
        ->name('collectors.index');
    /**
     * TODO: What?
     */
    /*
    Route::resource('collectors', \App\Http\Controllers\CollectorController::class)
        ->except(['show', 'index', 'store', 'create', 'update', 'destroy']);
*/

    Route::get('tenants', \App\Livewire\Tenant\ListResource::class)
        ->name('tenants.index');
    /*
    Route::get('tenants/create', \App\Livewire\Tenant\CreateResource::class)
        ->name('tenants.create');
*/
    Route::get('reports', \App\Livewire\Report\ListResource::class)
        ->name('reports.index');

    Route::get('reports/create', \App\Livewire\Report\CreateResource::class)
        ->name('reports.create');

    Route::get('tenants/{tenant}/reports', \App\Livewire\Report\ListResource::class)
        ->name('tenants.reports.index');


    Route::get('analytics', function() {
        return view('analytics');
    })->name('analytics');
    Route::get('profile', [\App\Http\Controllers\ProfileController::class, 'edit'])->name('profile');
    Route::get('profilasdfe', function() {

    })->name('company.settings');
    Route::get('asdf', function() {

    })->name('billing');

    Route::get('team', function() {

    })->name('team');

    Route::get('support', function() {

    })->name('support');

    Route::get('users', \App\Livewire\User\ListResource::class)
        ->name('users.index');

    Route::get('tenant/{tenant}/users/create', \App\Livewire\User\CreateResource::class)
        ->name('tenants.users.create');
    Route::get('users/create', \App\Livewire\User\CreateResource::class)
        ->name('users.create');


});

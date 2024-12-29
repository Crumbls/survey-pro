<?php

use Illuminate\Support\Facades\Route;

Route::group([
    'middleware' => ['auth']
], function() {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })
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

    Route::resource('collectors', \App\Http\Controllers\CollectorController::class);

    Route::get('surveys', \App\Livewire\Survey\ListResource::class)->name('surveys.index');

    Route::get('surveys/{record}/collectors', \App\Livewire\Collector\ListResource::class)
        ->name('survey.collectors.index');

    Route::get('surveys/{record}/collectors/create', [\App\Http\Controllers\ReportCollectorController::class, 'create'])
        ->name('surveys.collectors.create');

    Route::resource('surveys', \App\Http\Controllers\SurveyController::class)->except(['index'])
        ->parameters([
            'survey' => 'record'
        ]);

    Route::resource('reports', \App\Http\Controllers\ReportController::class);

    Route::get('analytics', function() {
        return view('analytics');
    })->name('analytics');
    Route::get('profile', function() {

    })->name('profile');
    Route::get('profilasdfe', function() {

    })->name('company.settings');
    Route::get('asdf', function() {

    })->name('billing');

    Route::get('team', function() {

    })->name('team');

    Route::get('support', function() {

    })->name('support');

});

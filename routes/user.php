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
    Route::get('surveys/{surveyId}/collectors/create', \App\Livewire\Collector\CreateResource::class)
        ->name('surveys.collectors.create');


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
    Route::get('surveys', \App\Livewire\Survey\ListResource::class)->name('surveys.index');

    Route::get('surveys/{surveyId}/collectors', \App\Livewire\Collector\ListResource::class)
        ->name('surveys.collectors.index');

    Route::get('surveys/{surveyId}/responses', \App\Livewire\Response\ListResource::class)
        ->name('survey.responses.index');


    Route::get('surveys/{record}/reports', \App\Livewire\Report\ListResource::class)
        ->name('survey.reports.index');

    Route::get('surveys/{surveyId}/reports/create', \App\Livewire\Report\CreateFromSurveyResource::class)
        ->name('surveys.reports.create-from-survey');

    Route::resource('surveys', \App\Http\Controllers\SurveyController::class)
        ->except(['index'])
        ->parameters([
            'survey' => 'record'
        ]);

    Route::get('reports', \App\Livewire\Report\ListResource::class)
        ->name('reports.index');

    Route::get('reports/create', \App\Livewire\Report\CreateResource::class)
        ->name('reports.create');

    Route::resource('reports', \App\Http\Controllers\ReportController::class)
        ->except(['index', 'create'])
        ->parameters([
            'report' => 'record'
        ]);


    Route::get('tenants', \App\Livewire\Tenant\ListResource::class)
        ->name('tenants.index');
    /*
    Route::get('tenants/create', \App\Livewire\Tenant\CreateResource::class)
        ->name('tenants.create');
*/

    Route::resource('tenants', \App\Http\Controllers\TenantController::class)
        ->except(['index','create','store','update'])
        ->parameters([
            'tenant' => 'record'
        ]);

    Route::get('tenants/{tenantId}/reports', \App\Livewire\Report\ListResource::class)
        ->name('tenants.reports.index');

    Route::get('tenants/{tenantId}/collectors', \App\Livewire\Collector\ListResource::class)
        ->name('tenants.collectors.index');

    Route::get('tenants/{tenantId}/surveys', \App\Livewire\Survey\ListResource::class)->name('tenants.surveys.index');
    Route::get('tenants/{tenantId}/surveys/create', \App\Livewire\Survey\CreateResource::class)
        ->name('tenants.surveys.create');
    Route::get('tenants/{tenantId}/reports/create', \App\Livewire\Report\CreateResource::class)
        ->name('tenants.reports.create');

    Route::get('tenants/{tenantId}/users', \App\Livewire\User\ListResource::class)
        ->name('tenants.users.index');

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

    Route::get('tenant/{tenantId}/users/create', \App\Livewire\User\CreateResource::class)
        ->name('tenants.users.create');
    Route::get('users/create', \App\Livewire\User\CreateResource::class)
        ->name('users.create');


});

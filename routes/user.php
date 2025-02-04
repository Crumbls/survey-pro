<?php

use Illuminate\Support\Facades\Route;

Route::group([
    'middleware' => [
        'web'
//        'auth'
    ]
], function() {
   Route::get('debug', function() {
       $user = request()->user();

       if (!$user) {
           $user = \App\Models\User::inRandomOrder()->take(1)->first();
           \Auth::login($user);
       }

       $service = app(\App\Services\TenantService::class);

       if ($user->tenants->isEmpty()) {
           $service->getOrCreateDefault($user);
           $user->load('tenants');
       }

       $tenant = $user->tenants->first();

       $service->createDefaultRolesPermissions($tenant);

       $role = \Silber\Bouncer\Database\Role::withoutGlobalScopes()->firstOrCreate([
           'name' => 'tenant-owner',
           'scope' => $tenant->getKey()
       ], [
           'title' => 'Center Owner',
       ]);

       \Silber\Bouncer\BouncerFacade::scope()->to($tenant->getKey());

       if (!$user->roles()->where('roles.id',$role->getKey())->exists()) {
           $role->assignTo($user);
       }


       print_r($user->roles()->get()->toArray());
   });
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

    Route::get('surveys', \App\Livewire\Survey\ListResource::class)
        ->name('surveys.index');
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


    Route::get('users/{user}/edit', \App\Livewire\User\EditResource::class)
        ->name('users.edit');

});

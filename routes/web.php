<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    $features = [
        [
            'icon' => asset('images/icons/production-analytics.svg'),
            'title' => 'Production Analytics',
            'description' => 'Track key production metrics and identify improvement opportunities with real-time data analysis.'
        ],
        [
            'icon' => asset('images/icons/efficiency-monitoring.svg'),
            'title' => 'Efficiency Monitoring',
            'description' => 'Measure and optimize production line efficiency with automated performance tracking.'
        ],
        [
            'icon' => asset('images/icons/process-optimization.svg'),
            'title' => 'Process Optimization',
            'description' => 'Streamline manufacturing workflows and reduce downtime through data-driven insights.'
        ],
        [
            'icon' => asset('images/icons/output-enhancement.svg'),
            'title' => 'Output Enhancement',
            'description' => 'Boost production output by identifying and eliminating bottlenecks in your processes.'
        ]
        // Add other features...
    ];

    return view('home', compact('features'));
})->name('home');

Route::get('/r/{collector}', [\App\Http\Controllers\CollectorController::class, 'show'])
    ->name('collector.show');

Route::post('/{collector}/responses/create', [\App\Http\Controllers\ResponseController::class, 'create'])
    ->name('responses.create');

Route::patch('/responses/{record}', [\App\Http\Controllers\ResponseController::class, 'update'])
    ->name('responses.update');

Route::get('terms-of-service', function() {
    return view('terms-of-service');
})->name('terms-of-service');


Route::get('privacy-policy', function() {
    return view('privacy-policy');
})->name('privacy-policy');

require __DIR__.'/user.php';


Route::group([
    'prefix' => 'tenants/{tenant}',
    'as' => 'tenants.',
    'middleware' => [
        'web',
        'auth',
//        \App\Http\Middleware\ScopeBouncer::class,
        \App\Http\Middleware\TenantMember::class
    ]
], function () {
    require_once(__DIR__.'/tenant.php');
});


Route::group([
    'prefix' => 'clients/{client}',
    'as' => 'clients.',
    'middleware' => [
        'web',
        'auth',
        \App\Http\Middleware\ClientMember::class,
//        \App\Http\Middleware\TenantMember::class
    ]
], function () {
    require_once(__DIR__.'/client.php');
});

Route::group([
    'prefix' => 'products/{product}',
    'as' => 'products.',
    'middleware' => [
        'web',
        'auth',
//        \App\Http\Middleware\ClientMember::class,
//        \App\Http\Middleware\TenantMember::class
    ]
], function () {
    require_once(__DIR__.'/product.php');
});


Route::group([
    'prefix' => 'surveys/{survey}',
    'as' => 'surveys.',
    'middleware' => [
        'web',
        'auth',
//        \App\Http\Middleware\ClientMember::class,
//        \App\Http\Middleware\TenantMember::class
    ]
], function () {
    require_once(__DIR__.'/survey.php');
});


Route::group([
    'prefix' => 'reports/{report}',
    'as' => 'reports.',
    'middleware' => [
        'web',
        'auth',
        \App\Http\Middleware\TenantMemberThroughReport::class
    ]
], function () {
    require_once(__DIR__.'/report.php');
});

require __DIR__.'/auth.php';



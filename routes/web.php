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

require __DIR__.'/user.php';
require __DIR__.'/auth.php';

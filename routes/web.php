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

Route::get('/r/{record}', [\App\Http\Controllers\CollectorController::class, 'show'])
    ->name('collector.show');

Route::get('test', function() {
/*
// Create a server node
    $server = \Crumbls\Infrastructure\Models\Node::create([
        'name' => 'Primary Server',
        'type' => 'server',
        'status' => 'operational',
        'metadata' => [
            'ip' => '192.168.1.1',
            'location' => 'us-east-1'
        ]
    ]);

// Add child nodes
    $database = \Crumbls\Infrastructure\Models\Node::create([
        'name' => 'Main Database',
        'type' => 'database',
        'status' => 'operational'
    ]);

    $server->children()->attach($database->id);
*/
    \Crumbls\Infrastructure\Models\Node::all()->each(function($node) {
//        $node->delete();
    });

    $nodes = \Crumbls\Infrastructure\Models\Node::with(['parents', 'children'])->get();

    $nodesForVis = $nodes->map(function($node) {
        return [
            'id' => $node->id,
            'label' => $node->name,
            'group' => $node->type,
            'color' => config("infrastructure.nodes.statuses.{$node->status}"),
            'title' => $node->metadata['error'] ?? "Status: {$node->status}"
        ];
    });

    $edges = collect();
    $nodes->each(function($node) use ($edges) {
        $node->children->each(function($child) use ($edges, $node) {
            $edges->push([
                'from' => $node->id,
                'to' => $child->id,
                'color' => ['color' => config("infrastructure.nodes.statuses.{$node->status}")]
            ]);
        });
    });

    return view('infrastructure::map', [
        'nodes' => $nodesForVis,
        'edges' => $edges
    ]);
});
Route::get('/test1', function () {
    return view('builder');
});

Route::post('/{collector}/responses/create', [\App\Http\Controllers\ResponseController::class, 'create'])
    ->name('responses.create');

Route::patch('/responses/{record}', [\App\Http\Controllers\ResponseController::class, 'update'])
    ->name('responses.update');

require __DIR__.'/user.php';
require __DIR__.'/auth.php';



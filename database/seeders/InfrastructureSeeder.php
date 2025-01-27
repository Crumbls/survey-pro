<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Crumbls\Infrastructure\Models\Node;

class InfrastructureSeeder extends Seeder
{
    public function run()
    {
        dd(Node::all());
        // Create infrastructure components
        $servers = Node::factory()->server()->count(10)->create();
        $databases = Node::factory()->database()->count(5)->create();
        $sites = Node::factory()->site()->count(50)->create();

        // Connect sites to random servers and databases
        $sites->each(function($site) use ($servers, $databases) {
            // Each site connects to 1-2 servers
            $selectedServers = $servers->random(fake()->numberBetween(1, 2));
            $site->children()->attach($selectedServers->pluck('id'));

            // Each site connects to 1-2 databases
            $selectedDatabases = $databases->random(fake()->numberBetween(1, 2));
            $site->children()->attach($selectedDatabases->pluck('id'));

            // If site has issues, propagate to connected infrastructure
            if ($site->status !== 'operational') {
                $selectedServers->each(function($server) use ($site) {
                    $server->status = $site->status;
                    $server->save();
                });

                $selectedDatabases->each(function($database) use ($site) {
                    $database->status = $site->status;
                    $database->save();
                });
            }
        });
    }
}

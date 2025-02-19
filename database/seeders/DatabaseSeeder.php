<?php

namespace Database\Seeders;

use Crumbls\Infrastructure\Database\Seeders\InfrastructureSeeder;
use Crumbls\Infrastructure\Models\Node;
use Crumbls\Issue\Database\Factories\IssueFactory;
use Crumbls\Issue\Models\Issue;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        Issue::factory()->create();

        exit;
        // \App\Models\User::factory(10)->create();
        //      $tenant = Tenant::factory(1)->create();
        //    return;

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);


            $this->call([
//                InfrastructureSeeder::class
                SubscriptionSeeder::class
            ]);

    }
}

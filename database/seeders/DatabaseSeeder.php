<?php

namespace Database\Seeders;

use Crumbls\Infrastructure\Database\Seeders\InfrastructureSeeder;
use Crumbls\Infrastructure\Models\Node;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
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

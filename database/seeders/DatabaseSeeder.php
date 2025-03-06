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
//        Issue::factory()->create();

            $this->call([
//                RoleTemplateSeeder::class,
UserSeeder::class,
//                InfrastructureSeeder::class
//                SubscriptionSeeder::class
            ]);

    }
}

<?php

namespace Database\Seeders;

use App\Models\Plan;
use Illuminate\Database\Seeder;
use Crumbls\Infrastructure\Models\Node;

class SubscriptionSeeder extends Seeder
{
    public function run()
    {
        Plan::withTrashed()->get()->each(function (Plan $plan) {
//            $plan->forceDelete();
        });

            $plan = Plan::firstOrCreate([
                'name' => 'Basic',
            ], [
                'description' => 'Basic plan',
                'price' => 99.99,
                'currency' => 'USD',
                'invoice_period' => 1,
                'invoice_interval' => 'year',
                'trial_period' => 14,
                'trial_interval' => 'day'
            ]);

    }

}

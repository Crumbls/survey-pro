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
            $plan->forceDelete();
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

        if (!$plan->features->count()) {
            $plan->features()->create([
                'name' => 'clients',
                'value' => 1,
                'resettable_period' => 1,
                'resettable_interval' => 'year'
            ]);
        }

return;
        // Create Basic Plan
        $basic = Plan::create([
            'name' => 'Basic Plan',
            'description' => 'Perfect for small businesses',
            'price' => 29.99,
            'signup_fee' => 0,
            'currency' => 'USD',
            'invoice_period' => 1,
            'invoice_interval' => 'month',
            'trial_period' => 14,
            'trial_interval' => 'day',
            'sort_order' => 1,
            'is_active' => true
        ]);

        // Basic Plan Features
        $basic->features()->createMany([
            [
                'name' => 'Projects',
                'description' => 'Number of active projects',
                'value' => '10',
                'resettable_period' => 1,
                'resettable_interval' => 'month',
                'sort_order' => 1
            ],
            [
                'name' => 'Storage Space',
                'description' => 'Storage space in GB',
                'value' => '5',
                'sort_order' => 2
            ],
            [
                'name' => 'Team Members',
                'description' => 'Number of team members',
                'value' => '3',
                'sort_order' => 3
            ]
        ]);

        // Create Pro Plan
        $pro = Plan::create([
            'name' => 'Pro Plan',
            'description' => 'Best for growing teams',
            'price' => 99.99,
            'signup_fee' => 0,
            'currency' => 'USD',
            'invoice_period' => 1,
            'invoice_interval' => 'month',
            'trial_period' => 14,
            'trial_interval' => 'day',
            'sort_order' => 2,
            'is_active' => true
        ]);

        // Pro Plan Features
        $pro->features()->createMany([
            [
                'name' => 'Projects',
                'description' => 'Number of active projects',
                'value' => '50',
                'resettable_period' => 1,
                'resettable_interval' => 'month',
                'sort_order' => 1
            ],
            [
                'name' => 'Storage Space',
                'description' => 'Storage space in GB',
                'value' => '50',
                'sort_order' => 2
            ],
            [
                'name' => 'Team Members',
                'description' => 'Number of team members',
                'value' => '10',
                'sort_order' => 3
            ],
            [
                'name' => 'API Access',
                'description' => 'Full API access',
                'value' => 'true',
                'sort_order' => 4
            ]
        ]);

        // Create Enterprise Plan
        $enterprise = Plan::create([
            'name' => 'Enterprise Plan',
            'description' => 'For large organizations',
            'price' => 299.99,
            'signup_fee' => 99.99,
            'currency' => 'USD',
            'invoice_period' => 1,
            'invoice_interval' => 'month',
            'trial_period' => 14,
            'trial_interval' => 'day',
            'sort_order' => 3,
            'is_active' => true
        ]);

        // Enterprise Plan Features
        $enterprise->features()->createMany([
            [
                'name' => 'Projects',
                'description' => 'Number of active projects',
                'value' => '-1', // Unlimited
                'resettable_period' => 1,
                'resettable_interval' => 'month',
                'sort_order' => 1
            ],
            [
                'name' => 'Storage Space',
                'description' => 'Storage space in GB',
                'value' => '1000',
                'sort_order' => 2
            ],
            [
                'name' => 'Team Members',
                'description' => 'Number of team members',
                'value' => '-1', // Unlimited
                'sort_order' => 3
            ],
            [
                'name' => 'API Access',
                'description' => 'Full API access',
                'value' => 'true',
                'sort_order' => 4
            ],
            [
                'name' => 'Dedicated Support',
                'description' => '24/7 priority support',
                'value' => 'true',
                'sort_order' => 5
            ]
        ]);
    }

}

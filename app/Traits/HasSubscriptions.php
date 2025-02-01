<?php

namespace App\Traits;

use App\Models\PlanSubscription;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait HasSubscriptions
{
    public function subscriptions(): MorphMany
    {
        return $this->morphMany(PlanSubscription::class, 'subscriber');
    }

    public function newSubscription(string $name, Plan $plan): PlanSubscription
    {
        $subscription = $this->subscriptions()->create([
            'plan_id' => $plan->id,
            'name' => $name,
            'starts_at' => Carbon::now(),
            'trial_ends_at' => $plan->trial_period ? Carbon::now()->addDays($plan->trial_period) : null,
        ]);

        // Create subscription features
        foreach ($plan->features as $feature) {
            $subscription->features()->create([
                'name' => $feature->name,
                'value' => $feature->value,
                'resettable_period' => $feature->resettable_period,
                'resettable_interval' => $feature->resettable_interval,
                'sort_order' => $feature->sort_order
            ]);
        }

        return $subscription;
    }
}

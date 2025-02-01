<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class PlanSubscriptionFeature extends Model
{
    use HasUuids;

    protected $fillable = [
        'plan_subscription_id',
        'name',
        'description',
        'value',
        'resettable_period',
        'resettable_interval',
        'sort_order'
    ];

    protected $casts = [
        'value' => 'integer',
        'resettable_period' => 'integer',
        'sort_order' => 'integer'
    ];

    public function subscription()
    {
        return $this->belongsTo(PlanSubscription::class, 'plan_subscription_id');
    }

    public function usage()
    {
        return $this->hasMany(PlanSubscriptionUsage::class);
    }
}

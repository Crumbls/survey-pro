<?php

namespace App\Models;

use App\Traits\HasUuid;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;

class PlanFeature extends Model
{
    use HasFactory,
        HasUuid,
        SoftDeletes;

    protected $fillable = [
        'plan_id',
        'uuid',
        'name',
        'description',
        'value',
        'resettable_period',
        'resettable_interval',
        'sort_order',
    ];

    protected $casts = [
        'plan_id' => 'integer',
        'uuid' => 'string',
        'name' => 'string',
        'description' => 'string',
        'value' => 'string',
        'resettable_period' => 'integer',
        'resettable_interval' => 'string',
        'sort_order' => 'integer',
        'deleted_at' => 'datetime',
    ];

    protected $observables = [
        'validating',
        'validated',
    ];

    protected static function boot()
    {
        parent::boot();


        static::deleted(function ($planFeature) {
            $planFeature->usage()->delete();
        });

    }
    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class);
    }

    public function usage(): HasMany
    {
        return $this->hasMany(PlanSubscriptionUsage::class, 'feature_id');
    }

    public function scopeByPlanId(Builder $builder, int $planId): Builder
    {
        return $builder->where('plan_id', $planId);
    }

    public function scopeOrdered(Builder $builder, string $direction = 'asc'): Builder
    {
        return $builder->orderBy('sort_order', $direction);
    }

    public function getResetDate(Carbon $dateFrom = null): Carbon
    {
        $dateFrom = $dateFrom ?? now();

        return $dateFrom->copy()->add(
            $this->resettable_interval,
            $this->resettable_period
        );
    }
}

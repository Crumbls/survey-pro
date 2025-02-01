<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PlanSubscriptionUsage extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'subscription_id',
        'feature_id',
        'used',
        'valid_until',
    ];

    protected $casts = [
        'subscription_id' => 'integer',
        'feature_id' => 'integer',
        'used' => 'integer',
        'valid_until' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    protected $observables = [
        'validating',
        'validated',
    ];

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($model) {
            $model->validateModel();
        });
    }

    protected function validateModel()
    {
        $rules = [
            'subscription_id' => 'required|integer|exists:plan_subscriptions,id',
            'feature_id' => 'required|integer|exists:plan_features,id',
            'used' => 'required|integer',
            'valid_until' => 'nullable|date',
        ];

        validator($this->attributes, $rules)->validate();
    }

    public function feature(): BelongsTo
    {
        return $this->belongsTo(PlanFeature::class, 'feature_id');
    }

    public function subscription(): BelongsTo
    {
        return $this->belongsTo(PlanSubscription::class, 'subscription_id');
    }

    public function scopeByFeatureUuid(Builder $builder, string $featureUuid): Builder
    {
        $feature = PlanFeature::where('uuid', $featureUuid)->first();
        return $builder->where('feature_id', $feature ? $feature->id : null);
    }

    public function expired(): bool
    {
        if (is_null($this->valid_until)) {
            return false;
        }

        return Carbon::now()->gte($this->valid_until);
    }
}

<?php

namespace App\Models;

use DB;
use Carbon\Carbon;
use LogicException;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\HasUuid;

class PlanSubscription extends Model
{
    use HasFactory,
//        HasUuid,
        SoftDeletes;

    protected $fillable = [
        'subscriber_id',
        'subscriber_type',
        'plan_id',
        'uuid',
        'name',
        'description',
        'trial_ends_at',
        'starts_at',
        'ends_at',
        'cancels_at',
        'canceled_at',
    ];

    protected $casts = [
        'subscriber_id' => 'integer',
        'subscriber_type' => 'string',
        'plan_id' => 'integer',
        'uuid' => 'string',
        'name' => 'json',
        'description' => 'json',
        'trial_ends_at' => 'datetime',
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'cancels_at' => 'datetime',
        'canceled_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    protected $observables = [
//        'validating',
  //      'validated',
    ];

    protected static function boot()
    {
        parent::boot();

        static::deleted(function ($subscription) {
            $subscription->usage()->delete();
        });

    }

    public function getTranslation(string $attribute, ?string $locale = null, bool $fallback = true)
    {
        $locale = $locale ?? app()->getLocale();
        $value = json_decode($this->attributes[$attribute] ?? '{}', true);

        if (isset($value[$locale])) {
            return $value[$locale];
        }

        if ($fallback) {
            return $value[config('app.fallback_locale')] ?? '';
        }

        return '';
    }

    public function setTranslation(string $attribute, string $locale, $value)
    {
        $translations = json_decode($this->attributes[$attribute] ?? '{}', true);
        $translations[$locale] = $value;
        $this->attributes[$attribute] = json_encode($translations);

        return $this;
    }

    public function subscriber(): MorphTo
    {
        return $this->morphTo('subscriber', 'subscriber_type', 'subscriber_id', 'id');
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class);
    }

    public function usage(): HasMany
    {
        return $this->hasMany(PlanSubscriptionUsage::class, 'subscription_id');
    }

    public function active(): bool
    {
        return ! $this->ended() || $this->onTrial();
    }

    public function inactive(): bool
    {
        return ! $this->active();
    }

    public function onTrial(): bool
    {
        return $this->trial_ends_at ? Carbon::now()->lt($this->trial_ends_at) : false;
    }

    public function canceled(): bool
    {
        return $this->canceled_at ? Carbon::now()->gte($this->canceled_at) : false;
    }

    public function ended(): bool
    {
        return $this->ends_at ? Carbon::now()->gte($this->ends_at) : false;
    }

    public function cancel($immediately = false)
    {
        $this->canceled_at = Carbon::now();

        if ($immediately) {
            $this->ends_at = $this->canceled_at;
        }

        $this->save();

        return $this;
    }

    public function changePlan(Plan $plan)
    {
        if ($this->plan->invoice_interval !== $plan->invoice_interval ||
            $this->plan->invoice_period !== $plan->invoice_period) {
            $this->setNewPeriod($plan->invoice_interval, $plan->invoice_period);
            $this->usage()->delete();
        }

        $this->plan_id = $plan->id;
        $this->save();

        return $this;
    }

    public function renew()
    {
        if ($this->ended() && $this->canceled()) {
            throw new LogicException('Unable to renew canceled ended subscription.');
        }

        $subscription = $this;

        DB::transaction(function () use ($subscription) {
            $subscription->usage()->delete();
            $subscription->setNewPeriod();
            $subscription->canceled_at = null;
            $subscription->save();
        });

        return $this;
    }

    public function scopeOfSubscriber(Builder $builder, Model $subscriber): Builder
    {
        return $builder->where('subscriber_type', $subscriber->getMorphClass())
            ->where('subscriber_id', $subscriber->getKey());
    }

    public function scopeFindEndingTrial(Builder $builder, int $dayRange = 3): Builder
    {
        return $builder->whereBetween('trial_ends_at', [
            Carbon::now(),
            Carbon::now()->addDays($dayRange)
        ]);
    }

    public function scopeFindEndedTrial(Builder $builder): Builder
    {
        return $builder->where('trial_ends_at', '<=', now());
    }

    public function scopeFindEndingPeriod(Builder $builder, int $dayRange = 3): Builder
    {
        return $builder->whereBetween('ends_at', [
            Carbon::now(),
            Carbon::now()->addDays($dayRange)
        ]);
    }

    public function scopeFindEndedPeriod(Builder $builder): Builder
    {
        return $builder->where('ends_at', '<=', now());
    }

    public function scopeFindActive(Builder $builder): Builder
    {
        return $builder->where('ends_at', '>', now());
    }

    protected function setNewPeriod($invoice_interval = '', $invoice_period = '', $start = '')
    {
        if (empty($invoice_interval)) {
            $invoice_interval = $this->plan->invoice_interval;
        }

        if (empty($invoice_period)) {
            $invoice_period = $this->plan->invoice_period;
        }

        $start = $start ?: now();

        switch ($invoice_interval) {
            case 'hour':
                $this->starts_at = $start;
                $this->ends_at = $start->copy()->addHours($invoice_period);
                break;
            case 'day':
                $this->starts_at = $start;
                $this->ends_at = $start->copy()->addDays($invoice_period);
                break;
            case 'week':
                $this->starts_at = $start;
                $this->ends_at = $start->copy()->addWeeks($invoice_period);
                break;
            case 'month':
                $this->starts_at = $start;
                $this->ends_at = $start->copy()->addMonths($invoice_period);
                break;
            case 'year':
                $this->starts_at = $start;
                $this->ends_at = $start->copy()->addYears($invoice_period);
                break;
        }

        return $this;
    }

    public function recordFeatureUsage(string $featureUuid, int $uses = 1, bool $incremental = true): PlanSubscriptionUsage
    {
        $feature = $this->plan->features()->where('uuid', $featureUuid)->first();

        $usage = $this->usage()->firstOrNew([
            'subscription_id' => $this->getKey(),
            'feature_id' => $feature->getKey(),
        ]);

        if ($feature->resettable_period) {
            if (is_null($usage->valid_until)) {
                $usage->valid_until = $feature->getResetDate($this->created_at);
            } elseif ($usage->expired()) {
                $usage->valid_until = $feature->getResetDate($usage->valid_until);
                $usage->used = 0;
            }
        }

        $usage->used = ($incremental ? $usage->used + $uses : $uses);
        $usage->save();

        return $usage;
    }

    public function reduceFeatureUsage(string $featureUuid, int $uses = 1): ?PlanSubscriptionUsage
    {
        $usage = $this->usage()->byFeatureuuid($featureUuid)->first();

        if (is_null($usage)) {
            return null;
        }

        $usage->used = max($usage->used - $uses, 0);
        $usage->save();

        return $usage;
    }

    public function canUseFeature(string $featureUuid): bool
    {
        $featureValue = $this->getFeatureValue($featureUuid);
        $usage = $this->usage()->byFeatureuuid($featureUuid)->first();

        if ($featureValue === 'true') {
            return true;
        }

        if (! $usage || $usage->expired() || is_null($featureValue) ||
            $featureValue === '0' || $featureValue === 'false') {
            return false;
        }

        return $this->getFeatureRemainings($featureUuid) > 0;
    }

    public function getFeatureUsage(string $featureUuid): int
    {
        $usage = $this->usage()->byFeatureuuid($featureUuid)->first();

        return (! $usage || $usage->expired()) ? 0 : $usage->used;
    }

    public function getFeatureRemainings(string $featureUuid): int
    {
        return $this->getFeatureValue($featureUuid) - $this->getFeatureUsage($featureUuid);
    }

    public function getFeatureValue(string $featureUuid)
    {
        $feature = $this->plan->features()->where('uuid', $featureUuid)->first();

        return $feature->value ?? null;
    }
}

<?php

namespace App\Models;

use App\Traits\HasSubscriptions;
use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Livewire\Features\SupportFileUploads\WithFileUploads;
use Masterix21\Addressable\Models\Concerns\HasAddresses;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Client extends Model implements HasMedia
{
    use HasAddresses,
        HasFactory,
        HasUuid,
        InteractsWithMedia,
        SoftDeletes,
        HasSubscriptions,
        WithFileUploads;

    protected $uuidFrom = 'name';

    protected $fillable = [
        'name',
        'uuid',

        'tenant_id',
        'primary_color',
        'secondary_color',
        'accent_color',
    ];

    public function tenant() : BelongsTo {
        return $this->belongsTo(Tenant::class);
    }

    public function collectors() : HasMany
    {
        return $this->hasMany(Collector::class);
    }

    public function reports() : HasMany
    {
        return $this->hasMany(Report::class);
    }

    public function surveys() : HasMany
    {
        return $this->hasMany(Survey::class);
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('logo')
            ->singleFile()
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/webp'])
            ->registerMediaConversions(function (Media $media) {
                $this->addMediaConversion('thumbnail')
                    ->width(200)
                    ->height(200);
            });
    }

    /**
     * Scope to get clients that:
     * 1. Have responses in their collectors
     * 2. Belong to user's authorized tenants
     * 3. Are sorted alphabetically by name
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param \App\Models\User $user
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWithResponsesForUser($query, User $user)
    {
        return $query->whereHas('collectors.responses')
            ->whereIn('tenant_id', $user->tenants()->select('tenants.id'))
            ->orderBy('name');
    }

    /**
     * Scope to get clients that:
     * 1. Have responses in their surveys
     * 2. Optionally filtered by tenant
     * 3. Sorted alphabetically by name
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string|null $tenantId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWithResponsesForTenant($query, Tenant|int $tenant)
    {
        return $query->whereHas('surveys.collectors.responses')
            ->where('tenant_id', is_int($tenant) ? $tenant : $tenant->getKey())
            ->orderBy('name', 'asc');
    }
}

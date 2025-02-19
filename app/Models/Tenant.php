<?php

namespace App\Models;

use App\Traits\HasSubscriptions;
use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Livewire\Features\SupportFileUploads\WithFileUploads;
use Masterix21\Addressable\Models\Concerns\HasAddresses;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Tenant extends Model implements HasMedia
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
        'primary_color',
        'secondary_color',
        'accent_color',
    ];

    public function roles()
    {
        return $this->hasMany(Role::class);
    }

    // If you want it as a relationship
    public function bouncerRoles()
    {
        return $this->hasMany(Role::class, 'scope', 'id');
    }

    public function users() : BelongsToMany
    {
        return $this->belongsToMany(User::class, 'tenant_user')
            ->withPivot('role_id')
            ->using(TenantUserRole::class);
    }

    public function clients() : HasMany {
        return $this->hasMany(Client::class);
    }

    public function products() : HasMany {
        return $this->hasMany(Product::class);
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
     * Scope to get tenants that:
     * 1. Have responses in their client hierarchy
     * 2. Are accessible to the given user
     * 3. Are sorted alphabetically by name
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param \App\Models\User $user
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWithResponsesForUser($query, User $user)
    {
        return $query->whereHas('clients.surveys.collectors.responses')
            ->whereIn('tenants.id', $user->tenants()->select('tenants.id'))
            ->orderBy('tenants.name');
    }

}

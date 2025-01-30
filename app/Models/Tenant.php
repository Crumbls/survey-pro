<?php

namespace App\Models;

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
        WithFileUploads;

    protected $uuidFrom = 'name';

    protected $fillable = [
        'name',
        'primary_color',
        'secondary_color',
        'accent_color',
    ];

    public function users() : BelongsToMany
    {
        return $this->belongsToMany(User::class, 'tenant_user_role')
            ->withPivot('role_id')
            ->using(TenantUserRole::class);
    }

    public function clients() : HasMany {
        return $this->hasMany(Client::class);
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

}

<?php

namespace App\Models;

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
        WithFileUploads;

    protected $uuidFrom = 'name';

    protected $fillable = [
        'name',
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

}

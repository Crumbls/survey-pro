<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Str;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Report extends Model implements HasMedia
{
    use HasFactory,
        InteractsWithMedia;

    protected $fillable = [
        'title',
        'survey_id',
        'user_id',
        'collector_ids',
        'data'
    ];

    protected $casts = [
        'collector_ids' => 'array',
        'data' => 'json'
    ];

    protected static function booted()
    {
        static::saving(function(Model $record) {
            if (!$record->data) {
                $record->data = [];
            }
        });

    }

    public function survey() : BelongsTo {
        return $this->belongsTo(Survey::class);
    }

    public function user() : BelongsTo {
        return $this->belongsTo(User::class);
    }

    public function registerMediaConversions(Media $media = null): void
    {
        $this->addMediaConversion('thumbnail')
            ->width(200)
            ->height(200);

        $this->addMediaConversion('preview')
            ->width(400)
            ->height(400);

        $this->addMediaConversion('optimized')
            ->width(1920)
            ->height(1080)
            ->nonQueued();
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('content_images')
            ->useDisk('public');
    }

}


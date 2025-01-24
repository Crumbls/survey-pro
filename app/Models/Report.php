<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Report extends Model
{
    use HasFactory;

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
}


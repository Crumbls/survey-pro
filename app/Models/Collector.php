<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Collector extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'type',
        'status',
        'configuration',
        'unique_code',
        'expires_at'
    ];

    protected $casts = [
        'configuration' => 'array',
        'expires_at' => 'datetime'
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function (Model $record) {
            if ($record->type === 'url' && !$record->unique_code) {
                $record->unique_code = $record->generateUniqueCode();
            }
            if (!$record->name) {
                $record->name = 'r/'.$record->unique_code;
            }
        });
    }

    public function getNameAttribute() : string {
        if (array_key_exists('name', $this->attributes) && $this->attributes['name']) {
            return $this->attributes['name'];
        }

        if ($this->type == 'url') {
            return '/r/'.$this->unique_code;
        }

        return '';
    }

    public function survey()
    {
        return $this->belongsTo(Survey::class);
    }

    public function responses()
    {
        return $this->hasMany(Response::class);
    }

    public function generateUniqueCode()
    {
        do {
            $code = Str::random(8);
        } while (static::where('survey_id', $this->survey_id)
            ->where('unique_code', $code)
            ->exists());

        return $code;
    }

    public function getUrlAttribute()
    {
        if ($this->type === 'url') {
            return route('survey.collect', [
                'survey' => $this->survey_id,
                'code' => $this->unique_code
            ]);
        }
        return null;
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active')
            ->where(function ($query) {
                $query->whereNull('expires_at')
                    ->orWhere('expires_at', '>', now());
            });
    }
}


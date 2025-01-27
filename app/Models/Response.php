<?php

// app/Models/Response.php
namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Response extends Model
{
    use HasFactory,
        HasUuid;

    protected $fillable = [
        'survey_id',
        'collector_id',
        'uuid',
        'data'
    ];

    protected $casts = [
        'data' => 'array'
    ];

    public static function booted() {
        static::creating(function(Model $record) {
            if (!$record->uuid) {
                $uuid = null;

                do {
                    $uuid = Str::uuid()->toString();
                } while (Response::where('uuid', $uuid)->take(1)->count());

                $record->uuid = $uuid;
            }
        });
    }

    public function survey()
    {
        return $this->belongsTo(Survey::class);
    }

    public function collector()
    {
        return $this->belongsTo(Collector::class);
    }
}

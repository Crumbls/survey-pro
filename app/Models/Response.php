<?php

// app/Models/Response.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Response extends Model
{
    use HasFactory;

    protected $fillable = [
        'survey_id',
        'collector_id',
        'data'
    ];

    protected $casts = [
        'data' => 'array'
    ];

    public function survey()
    {
        return $this->belongsTo(Survey::class);
    }

    public function collector()
    {
        return $this->belongsTo(Collector::class);
    }
}

<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Survey extends Model
{
    use HasFactory,
        HasUuid,
        SoftDeletes;

    protected $fillable = [
        'tenant_id',
        'user_id',
        'title',
        'description',
        'questions'
    ];

    protected $casts = [
        'questions' => 'array'
    ];

    public function tenant() : BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function user() : BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function collectors()
    {
        return $this->hasMany(Collector::class);
    }

    public function responses()
    {
        return $this->hasMany(Response::class);
    }

    public function reports() : HasMany {
        return $this->hasMany(Report::class);
    }

}

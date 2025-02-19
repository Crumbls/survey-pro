<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Collector extends Model
{
    use HasFactory,
        SoftDeletes;

    protected $fillable = [
        'name',
        'type',
        'status',
        'goal',
        'configuration',
        'unique_code',
        'expires_at',
        'survey_id',
        'client_id'
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

    public function survey() : BelongsTo
    {
        return $this->belongsTo(Survey::class);
    }

    public function responses() : HasMany
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

    public function scopeOpen($query)
    {
        return $query->where('status', 'open')
            ;
    }
    public function scopeClosed($query)
    {
        return $query->where('status', 'closed')
;
    }

    public function client() : BelongsTo {
        return $this->belongsTo(Client::class);
    }

    /**
     * Get collectors with responses, filtered by:
     * 1. User's authorized tenants
     * 2. Optional specific survey (ID or Model)
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param \App\Models\Survey|int|null $survey
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWithResponsesForUser($query, User $user, int|Survey $survey = null)
    {
        $query->whereIn('survey_id', function ($query) use ($user) {
            $query->select('id')
                ->from('surveys')
                ->whereIn('tenant_id', function ($subQuery) use ($user) {
                    $subQuery->select('tenants.id')
                        ->from('tenants')
                        ->join('tenant_user', 'tenants.id', '=', 'tenant_user.tenant_id')
                        ->where('tenant_user.user_id', $user->getKey());
                });
        })
            ->whereHas('responses');

        if ($survey) {
            $surveyId = $survey instanceof Survey ? $survey->getKey() : $survey;
            $query->where('survey_id', $surveyId);
        }

        return $query;


    }
}


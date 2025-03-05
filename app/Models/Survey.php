<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\SoftDeletes;

class Survey extends Model
{
    use HasFactory,
        HasUuid,
        SoftDeletes;

    protected $fillable = [
        'tenant_id',
        'client_id',
        'user_id',
        'title',
        'description',
        'questions'
    ];

    protected $casts = [
        'questions' => 'json'
    ];

    public function tenant() : BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function client() : BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function user() : BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function collectors() : HasMany
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

    public function getExcludedTypesQuestionCount() : array {
        return [
            'expression',
            'html',
            'image'
        ];
    }
    public function getQuestionCount() : int {
        return once(function() {

        try {
            $temp = $this->questions;
            if (!$temp) {
                return 0;
            }
            if (is_string($temp)) {
                $temp = json_decode($temp, true);
            } else if (is_object($temp)) {
                $temp = (array)$temp;
            }
            if (!array_key_exists('pages', $temp) || !is_array($temp['pages'])) {
                return 0;
            }
            $x = array_map(function($page) {
                if (!array_key_exists('elements', $page) || !is_array($page['elements'])) {
                    return 0;
                }

                return count(array_filter($page['elements'], function (array $element): int {
                    return !in_array($element['type'], $this->getExcludedTypesQuestionCount());
                }));
            }, $temp['pages']);
            return array_sum($x);
        } catch (\Throwable $e) {
            return 0;
        }
        });

    }

    /**
     * Scope to get surveys that:
     * 1. Have at least one response in their collectors
     * 2. Belong to clients within user's authorized tenants
     * 3. Are sorted alphabetically by title
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param \App\Models\User $user
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWithResponsesForUser($query, User $user)
    {
        return $query
            ->whereHas('collectors.responses')
            ->whereIn('client_id', function ($query) use ($user) {
                $query->select('id')
                    ->from('clients')
                    ->whereIn('tenant_id', $user->tenants()->select('tenants.id'));
            })
            ->orderBy('title');
    }
}

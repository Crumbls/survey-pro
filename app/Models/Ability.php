<?php

namespace App\Models;

use App\Services\AuthorizationCache;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

use Illuminate\Support\Facades\Cache;


class Ability extends Model
{
    protected $fillable = ['name', 'title', 'entity_type', 'entity_id', 'only_owned'];

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'role_abilities');
    }

    protected static function boot()
    {
        parent::boot();

        static::saved(function (Model $record) {
            app(AuthorizationCache::class)->clearRoleCache($record->getKey());
        });
    }
}

<?php

namespace App\Models;

use App\Services\AuthorizationCache;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\Pivot;

class Permission extends Pivot
{
    protected $table = 'permissions';
    public $timestamps = false;

    protected $fillable = [
        'ability_id',
        'role_id',
        'forbidden'
    ];

    public function ability() : BelongsTo
    {
        return $this->belongsTo(Ability::class);
    }

    public function role() : BelongsTo {
        return $this->belongsTo(Role::class);
    }

    protected static function boot()
    {
        parent::boot();

        return;

        static::saved(function ($permission) {
            /**
             * Caching service....
             */
            $cache = app(AuthorizationCache::class);

            if ($permission->entity_type === Role::class) {
                // Clear role cache since permissions might have changed
                $cache->clearRoleCache($permission->entity_id);
            } else if ($permission->entity_type === User::class) {
                // Clear user cache if this is a user-specific permission
                $cache->clearUserCache($permission->entity_id);
            }

        });
    }
}

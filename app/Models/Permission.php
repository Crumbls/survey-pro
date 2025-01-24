<?php

namespace App\Models;

use App\Services\AuthorizationCache;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Permission extends Model
{
    protected $fillable = ['ability_id', 'entity_type', 'entity_id', 'forbidden'];

    public function ability()
    {
        return $this->belongsTo(Ability::class);
    }

    protected static function boot()
    {
        parent::boot();

        static::saved(function ($permission) {
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

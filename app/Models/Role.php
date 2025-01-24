<?php

namespace App\Models;

use App\Services\AuthorizationCache;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

use Illuminate\Support\Facades\Cache;

class Role extends Model
{
    protected $fillable = ['name', 'title', 'level'];

    public function abilities()
    {
        return $this->belongsToMany(Ability::class, 'role_abilities');
    }

    public function permissions()
    {
        return $this->hasMany(Permission::class);
    }

    protected static function boot()
    {
        parent::boot();

        static::saved(function ($role) {
            $cache = app(AuthorizationCache::class);
            $cache->clearRoleCache($role->id);

            // Clear cache for all users with this role
            $role->users->each(function ($user) use ($cache) {
                $cache->clearUserCache($user->id);
            });
        });

        static::deleting(function ($role) {
            $cache = app(AuthorizationCache::class);

            // Clear all related caches before deletion
            $role->users->each(function ($user) use ($cache) {
                $cache->clearUserCache($user->id);
            });

            $cache->clearRoleCache($role->id);
        });
    }


    public function users() : BelongsToMany
    {
        return $this->belongsToMany(User::class, 'tenant_user_role')
            ->withPivot('role_id')
            ->using(TenantUserRole::class);
    }
}

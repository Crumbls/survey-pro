<?php

namespace App\Services;

use App\Models\TenantUserRole;
use Illuminate\Support\Facades\Cache;

class AuthorizationCache
{
    protected $ttl = 3600;

    protected function makeKey(...$parts): string
    {
        return 'auth:' . implode(':', array_filter($parts));
    }

    public function getUserRoles($userId, $tenantId)
    {
        return Cache::remember(
            $this->makeKey('user', $userId, 'tenant', $tenantId, 'roles'),
            $this->ttl,
            fn() => TenantUserRole::where(['user_id' => $userId, 'tenant_id' => $tenantId])
                ->with('role')
                ->get()
        );
    }

    public function getRoleAbilities($roleId)
    {
        return Cache::remember(
            $this->makeKey('role', $roleId, 'abilities'),
            $this->ttl,
            fn() => Role::with('abilities')->find($roleId)
        );
    }

    public function clearUserCache($userId, $tenantId = null)
    {
        $keys = [
            $this->makeKey('user', $userId, 'tenant', $tenantId, 'roles'),
            $this->makeKey('user', $userId, 'abilities'),
        ];

        foreach ($keys as $key) {
            Cache::forget($key);
        }
    }

    public function clearTenantCache($tenantId)
    {
        // You might want to store tenant-related keys in a set
        $userIds = TenantUserRole::where('tenant_id', $tenantId)
            ->pluck('user_id')
            ->unique();

        foreach ($userIds as $userId) {
            $this->clearUserCache($userId, $tenantId);
        }
    }

    public function clearRoleCache($roleId)
    {
        Cache::forget($this->makeKey('role', $roleId, 'abilities'));

        // Clear cache for all users with this role
        $userIds = TenantUserRole::where('role_id', $roleId)
            ->pluck('user_id')
            ->unique();

        foreach ($userIds as $userId) {
            $this->clearUserCache($userId);
        }
    }
}

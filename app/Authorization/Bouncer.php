<?php

namespace YourNamespace\Authorization;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Support\Collection;

class Bouncer
{
    protected $user;
    protected $roles;
    protected $tenantId;

    public function __construct($tenantId = null)
    {
        $this->tenantId = $tenantId;
    }

    public function assign(string $role, Model $user = null)
    {
        $user = $user ?: $this->user;
dd(__LINE__);
        return TenantUserRole::create([
            'tenant_id' => $this->tenantId,
            'user_id' => $user->id,
            'role_id' => $role
        ]);
    }

    public function retract(string $role, Model $user = null)
    {
        $user = $user ?: $this->user;
        dd(__LINE__);

        return TenantUserRole::where([
            'tenant_id' => $this->tenantId,
            'user_id' => $user->id,
            'role_id' => $role
        ])->delete();
    }

    public function getRoles(Model $user = null)
    {
        $user = $user ?: $this->user;
        dd(__LINE__);

        return TenantUserRole::where([
            'tenant_id' => $this->tenantId,
            'user_id' => $user->id
        ])->get();
    }

    public function hasRole($role, Model $user = null)
    {
        $user = $user ?: $this->user;
        dd(__LINE__);

        return TenantUserRole::where([
            'tenant_id' => $this->tenantId,
            'user_id' => $user->id,
            'role_id' => $role
        ])->exists();
    }

    public function setTenantId($tenantId)
    {
        $this->tenantId = $tenantId;
        return $this;
    }
}

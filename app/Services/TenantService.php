<?php

namespace App\Services;

// app/Services/TenantService.php
use App\Models\Role;
use App\Models\Tenant;
use App\Models\User;

class TenantService
{
    public function getOrCreateDefault(User $user): Tenant
    {
        // Check if user already has a tenant
        $existingTenant = $user->tenants()->first();

        if ($existingTenant) {
            return $existingTenant;
        }

        // Create new tenant with default naming convention
        $tenant = Tenant::create([
            'name' => $this->generateDefaultTenantName($user)
        ]);

        // Attach user to tenant with default admin role
        $role = Role::firstOrCreate(['name' => 'Tenant Owner']);

        $user->tenants()->attach($tenant->id, [
            'role_id' => $role->id,
        ]);

        return $tenant;
    }

    private function generateDefaultTenantName(User $user): string
    {
        // You can customize this based on your needs
        return $user->name . "'s Organization";
    }
}

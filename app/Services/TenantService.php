<?php

namespace App\Services;

// app/Services/TenantService.php
use App\Models\Client;
use App\Models\Collector;
use App\Models\Role;
use App\Models\RoleTemplate;
use App\Models\Survey;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use App\Models\Ability;

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

        if ($tenant->wasRecentlyCreated) {
            $this->createDefaultRolesPermissions($tenant);
        }

        $role = $tenant->roles()->firstOrCreate([
            'title' => 'Center Owner'
        ]);

        $user->tenants()->attach($tenant, ['role_id' => $role->id]);

        $user->save();

        return $tenant;
    }


    public function createDefaultRolesPermissions(Tenant $tenant) {

        $requiredRoles = $this->getRequiredRoles();

        $requiredRoles->keyBy('display_name')
            ->diff($tenant->roles->keyBy('title'))
            ->each(function($template) use ($tenant) {

                $role = Role::create([
                    'title' => $template->display_name,
                    'tenant_id' => $tenant->getKey(),
                    'description' => $template->description
                ]);

                if ($template->default_abilities) {
                    $abilityPatterns = $template->default_abilities;
                    // Build query for exact matches and wildcards
                    $query = Ability::where(function($q) use ($abilityPatterns) {
                        foreach ($abilityPatterns as $pattern) {
                            if ($pattern == '*') {
                                abort(500);
                            }
                            $pattern = explode(',', $pattern);
                            $x = count($pattern);
                            if (!$x) {
                                continue;
                            } else if ($x == 1) {
                                abort(500);
                            } else if ($x == 2) {
                                if ($pattern[1] == '*') {
                                    /**
                                     * Wildcard.
                                     */
                                    $q->orWhere('entity_type', $pattern[0]);
                                } else {
                                    $q->orWhere(function(Builder $sub) use ($pattern){
                                        $sub->where('entity_type', $pattern[0]);
                                        $sub->where('name', $pattern[1]);
                                    });
                                }
                            } else {
                                abort(500);
                            }

                        }
                    });

                    // Single query to get all matching abilities
                    $abilities = $query
                        ->whereNotIn('id',
                            \DB::table('permissions')
                                ->where('role_id', $role->getKey())
                                ->select('ability_id')
                        )
                        ->get()
                        ->each(function(Ability $ability) use ($role) {
                            \DB::table('permissions')
                                ->insert([
                                    'role_id' => $role->getKey(),
                                    'ability_id' => $ability->getKey()
                                ]);
                        });

                    /**
                     * TODO: Clear authorization cache.
                     */

                    /**
                     * TODO: Rebuild authorization cache.
                     */
                }
            });
    }

    private function generateDefaultTenantName(User $user): string
    {
        // You can customize this based on your needs
        return $user->name . "'s Organization";
    }

    /**
     * This is no longer correct.
     * @return Collection
     */
    public function getRequiredRoles() : Collection {
        return once(function() {
            return RoleTemplate::tenantSpecific()->get();
        });
    }
}

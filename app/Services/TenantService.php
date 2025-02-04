<?php

namespace App\Services;

// app/Services/TenantService.php
use App\Models\Client;
use App\Models\Collector;
use App\Models\Role;
use App\Models\Survey;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Silber\Bouncer\BouncerFacade;
use Silber\Bouncer\Database\Ability;

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

        BouncerFacade::scope()->to($tenant->getKey())->onlyRelations()->dontScopeRoleAbilities();

        /**
         * This won't work due to scoping issues.
         */
        // Attach user to tenant with default admin role
        /*
        $role = Role::firstOrCreate(['name' => 'center-owner'], ['title' => 'Center Owner']);

        $user->tenants()->attach($tenant->id);

        $user->assign($role);
*/
        if ($tenant->wasRecentlyCreated) {
            $this->createDefaultRolesPermissions($tenant);
        }

        return $tenant;
    }


    public function createDefaultRolesPermissions(Tenant $tenant) {
        $requiredRoles = $this->getRequiredRoles();

        /**
         * Add in new roles as necessary.
         */
        $requiredRoles->pluck('name')
            ->diff(
                \DB::table('roles')
                    ->where('scope', $tenant->getKey())
                    ->whereIn('name', $requiredRoles->pluck('name'))
                    ->select('name')
                    ->get()
                    ->pluck('name')
            )
            ->each(function($roleName) use ($requiredRoles, $tenant) {
                $role = $requiredRoles->get($roleName);
                \DB::table('roles')
                    ->insert([
                        'scope' => $tenant->getKey(),
                        'name' => $role->name,
                        'title' => $role->title,
                        'created_at' => now()
                    ]);
            });


        foreach($requiredRoles as $role) {
            $requiredPermissions = once(function() use ($role){
                return \DB::table('permissions')
                    ->where('entity_type', 'roles')
                    ->where('entity_id', $role->id)
                    ->whereNull('scope')
                    ->select('ability_id')
                    ->get()
                    ->pluck('ability_id');
            });

            $childRole = \DB::table('roles')
                ->where('scope', $tenant->getKey())
                ->where('name', $role->name)
                ->take(1)
                ->first();

            if (!$childRole) {
                continue;
            }

            /**
             * Clean up bad permissions.
             */
            if (true) {
                $temp = \DB::table('roles')
                    ->where('entity_type', 'roles')
                    ->where('entity_id', $childRole->id)
                    ->whereNotIn('ability_id',
                        \DB::table('permissions')
                            ->where('entity_type', 'roles')
                            ->where('entity_id', $role->id)
                            ->whereNull('scope')
                            ->select('ability_id')
                    )
                    ->get();

                if ($temp->count()) {
                    dd($temp);
                }

            }

            /**
             * Get missing.
             */
            \DB::table('permissions')
                ->where('entity_type', 'roles')
                ->where('entity_id', $role->id)
                ->whereNull('scope')
                ->select('ability_id')
                ->whereNotIn('ability_id',
                    \DB::table('permissions')
                        ->where('entity_type', 'roles')
                        ->where('entity_id', $childRole->id)
                        ->select('ability_id')
                )
                ->get()
                ->pluck('ability_id')
                ->each(function($ability) use ($childRole){
                    \DB::table('permissions')
                        ->insert([
                            'ability_id' => $ability,
                            'entity_type' => 'roles',
                            'entity_id' => $childRole->id,
                            'scope' => null
                        ]);
                });
        }
    }

    private function generateDefaultTenantName(User $user): string
    {
        // You can customize this based on your needs
        return $user->name . "'s Organization";
    }

    public function getRequiredRoles() : Collection {
        return once(function() {
            return \DB::table('roles')
                ->whereNull('scope')
                ->where('name', '<>', 'administrator')
                ->where('name','<>', 'center-owner')
                ->select(['name', 'title', 'id'])
                ->get()
                ->keyBy('name');
        });
    }
}

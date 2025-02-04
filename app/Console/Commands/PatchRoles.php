<?php

namespace App\Console\Commands;

use App\Models\Tenant;
use App\Services\TenantService;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use App\Services\SchemaService;

class PatchRoles extends Command
{
    protected $signature = 'patch:roles';
    protected $description = 'Patch roles and permissions for tenants';

    public function handle()
    {
        if (true) {
            $service = app(TenantService::class);
            $tenant = Tenant::orderBy('id','desc')->take(1)->first();
            dd($tenant->toArray());
            $service->createDefaultRolesPermissions($tenant);
            dd($tenant->toArray());
        } else {

            $this->createTenantRoles();

            $this->copyTenantPermissions();
        }


    }

    protected function getRequiredRoles() : Collection {
        return once(function() {
            return \DB::table('roles')
                ->whereNull('scope')
                ->where('name', '<>', 'administrator')
                ->select(['name', 'title', 'id'])
                ->get()
                ->keyBy('name');
        });
    }

    protected function createTenantRoles() : void {
        $requiredRoles = $this->getRequiredRoles();

        \DB::table('tenants')
            ->select('id')
            ->get()
            ->pluck('id')
            ->each(function($tenantId) use ($requiredRoles) {
                $existing = $requiredRoles->pluck('name')
                    ->diff(
                        \DB::table('roles')
                            ->where('scope', $tenantId)
                            ->whereIn('name', $requiredRoles->pluck('name'))
                            ->select('name')
                            ->get()
                            ->pluck('name')
                    )
                    ->each(function($roleName) use ($requiredRoles, $tenantId) {
                        $role = $requiredRoles->get($roleName);
                        \DB::table('roles')
                            ->insert([
                                'scope' => $tenantId,
                                'name' => $role->name,
                                'title' => $role->title,
                                'created_at' => now()
                            ]);

                    });
            });

        /**
         *
         */


    }

    protected function copyTenantPermissions() : void {
        $tenants = Tenant::all()->pluck('id');

        foreach($tenants as $tenant) {
            foreach($this->getRequiredRoles() as $role) {

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
                    ->where('scope', $tenant)
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
    }
}

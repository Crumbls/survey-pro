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
use Silber\Bouncer\BouncerFacade;
use Silber\Bouncer\Database\Ability;

class TenantService
{
    /**
     * Standard permissions to create for each model
     */
    private array $standardPermissions = [
        'viewAny',
        'view',
        'create',
        'update',
        'delete',
        'restore',
        'forceDelete',
    ];

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

        // Attach user to tenant with default admin role
        $role = Role::firstOrCreate(['name' => 'center-owner'], ['title' => 'Center Owner']);

        $user->tenants()->attach($tenant->id);

        $user->assign($role);

        if ($tenant->wasRecentlyCreated) {
            $this->createDefaultRolesPermissions($tenant);
        }

        return $tenant;
    }


    public function createDefaultRolesPermissions(Tenant $tenant) {
        BouncerFacade::scope()->to(null);


        $chunkA = [];
        foreach($this->getModels() as $model) {
            foreach($this->standardPermissions as $permission) {

           $chunkA[] = Ability::firstOrCreate([
               'name' => $permission,
               'title' => $permission.' '.class_basename($model),
               'entity_id' => null,
               'entity_type' => $model,
               'scope' => null
           ]);
            }
        }

        BouncerFacade::scope()->to($tenant->getKey());//->onlyRelations()->dontScopeRoleAbilities();

        $role = \Silber\Bouncer\Database\Role::firstOrCreate([
            'name' => 'tenant-owner',
            'scope' => $tenant->getKey()
        ], [
            'title' => 'Center Owner',
        ]);

        foreach($chunkA as $ability) {
//            print_r($ability->toArray());
            $role->allow($ability);
        }

        $role = \Silber\Bouncer\Database\Role::firstOrCreate([
            'name' => 'tenant-administrator',
            'scope' => $tenant->getKey()
        ], [
            'title' => 'Center Administrator',
        ]);


        foreach($chunkA as $ability) {
            $role->allow($ability);
        }



        $role = \Silber\Bouncer\Database\Role::firstOrCreate([
            'name' => 'tenant-facilitator',
            'scope' => $tenant->getKey()
        ], [
            'title' => 'Center Facilitator',
        ]);


        foreach($chunkA as $ability) {
            $role->allow($ability);
        }


        /**
         * We need to give them all of the appropriate permissions, which we have to figure out what is correct.
         */
    }

    private function generateDefaultTenantName(User $user): string
    {
        // You can customize this based on your needs
        return $user->name . "'s Organization";
    }

    public function getModels() : array {
        $files = glob(app_path('Models/*.php'));

        $modelClasses = [];

        foreach ($files as $file) {
            // Get the contents of the file
            $contents = file_get_contents($file);

            // Extract the namespace
            preg_match('/namespace\s+(.+?);/', $contents, $matches);

            $namespace = $matches[1] ?? null;

            if (!$namespace) {
                continue;
            }

            // Extract the class name
            preg_match('/class\s+(\w+)/', $contents, $matches);
            $className = $matches[1] ?? null;

            if (!$className) {
                continue;
            }

            // Construct the fully qualified class name
            $fullClassName = $namespace . '\\' . $className;

            try {
                // Use reflection to check if the class extends Model
                $reflection = new \ReflectionClass($fullClassName);

                if ($reflection->isSubclassOf(Model::class)) {
                    $modelClasses[] = $fullClassName;

                    // You can perform seeding operations for each model here
//                    $this->seedModel($fullClassName);
                }
            } catch (\Exception $e) {
                // Log any errors but continue processing
                \Log::warning("Failed to process class {$fullClassName}: " . $e->getMessage());
                continue;
            }
        }

        return $modelClasses;
    }

}

<?php

namespace Database\Seeders;

use App\Filament\Resources\PlanFeatureResource;
use App\Filament\Resources\PlanSubscriptionUsageResource;
use App\Models\Ability;
use App\Models\Client;
use App\Models\Collector;
use App\Models\Permission;
use App\Models\Plan;
use App\Models\PlanFeature;
use App\Models\PlanSubscription;
use App\Models\PlanSubscriptionFeature;
use App\Models\PlanSubscriptionUsage;
use App\Models\Report;
use App\Models\Response;
use App\Models\Role;
use App\Models\Survey;
use App\Models\Tenant;
use App\Models\TenantUser;
use App\Models\TenantUserRole;
use App\Models\User;
use App\Services\TenantService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;
use Crumbls\Infrastructure\Models\Node;
use Bouncer;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class RolePermissionSeeder extends Seeder
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

    public function run()
    {
        $models = $this->getModels();

        foreach ($models as $modelClass) {
            $this->createModelPermissions($modelClass);
        }

        $methods = preg_grep('#^role[A-Z]#', get_class_methods(get_called_class()));

        foreach($methods as $method) {
            $this->$method();
        }

        $user = User::firstOrCreate([
            'email' => 'chase@crumbls.com'
        ], [
            'name' => 'Chase Miller',
            'password' => Hash::make('password')
        ]);

        Bouncer::assign('administrator')->to($user);


        return;

//        Bouncer::allow($admin)->to('viewAny', Role::class);

        Bouncer::allow($admin)->everything();

        $temp = \Silber\Bouncer\Database\Role::withoutGlobalScopes()
            ->where('name','tenant-owner')
            ->get();

        foreach($temp as $role) {
            Bouncer::allow($role)->everything();
            Bouncer::forbid($role)->to('access-filament');
            Bouncer::forbid($role)->to('view-analytics');
        }

        $temp = \Silber\Bouncer\Database\Role::withoutGlobalScopes()
            ->where('name','tenant-member')
            ->get();

        foreach($temp as $role) {
            Bouncer::allow($role)->everything();
            Bouncer::forbid($role)->to('access-filament');
            Bouncer::forbid($role)->to('view-analytics');
        }


        exit;

        dd($temp);

        /**
         * Sorry about this!
         */
        Tenant::where('uuid', 'jayme-sitzmans-organization')->get()->each(function(Tenant $tenant) {
            $service = app(TenantService::class);
            $service->createDefaultRolesPermissions($tenant);
        });

return;


        dd(Role::all()->pluck('name'));

    }

    protected function roleAdministrator() : void {
        $role = Bouncer::role()->firstOrCreate([
            'name' => 'administrator',
            'title' => 'Administrator',
        ]);

        Bouncer::allow($role)->to('access-filament');
        Bouncer::allow($role)->everything();

    }

    protected function getAllAbilities() : Collection {
        return once(function() {
           return \DB::table('abilities')
                ->whereNull('scope')
                ->whereNull('entity_id')
               ->select('id')
                ->get()
               ->pluck('id');
        });
        dd(__LINE__);
    }

    public function getTenantOwnerAbilitiesExcluded() : Collection {
        return once(function() {
            return \DB::table('abilities')
                ->where(function($sub) {
                    $sub->where(function($inner) {
                        $inner->where('name','create');
                        $inner->whereIn('entity_type', [
                            Tenant::class,
                            TenantUser::class,
                        ]);
                    });
                })
                ->select('id')
                ->get()
                ->pluck('id')
                ->merge(\DB::table('abilities')
                    ->whereIn('entity_type', [
                        Ability::class,
                        Permission::class,
                        Plan::class,
                        PlanFeature::class,
                        PlanSubscription::class,
                        PlanSubscriptionUsage::class,
                        PlanSubscriptionFeature::class,
                        Role::class,
//                        Tenant::class,
                        TenantUser::class,
                        TenantUserRole::class,
                    ])
                    ->orWhere('name','access-filament')
                    ->orWhere('name','*')
                    ->orWhere('name','restore')
                    ->orWhere('name','forceDelete')
                    ->select('id')
                    ->get()
                    ->pluck('id')
                )
                ->merge(\DB::table('abilities')
                    ->whereIn('entity_type', [
                        User::class,
                        Tenant::class,
                    ])
                    ->where('name','delete')
                    ->select('id')
                    ->get()
                    ->pluck('id')
                )->unique();
        });
    }
    protected function roleTenantOwner() : void {
        $role = Role::firstOrCreate(['name' => 'tenant-owner'], [
            'title' => 'Center Owner'
        ]);

        // Get all ability IDs
        $all = $this->getAllAbilities();

        $excluded = $this->getTenantOwnerAbilitiesExcluded();


        $current = \DB::table('abilities')
                ->whereNull('scope')
                ->whereNull('entity_id')
                ->whereIn('abilities.id',
                    \DB::table('permissions')
                        ->where('permissions.entity_type', 'roles')
                        ->where('permissions.entity_id', $role->getKey())
                        ->select('permissions.ability_id')
                )
                ->select('id')
                ->get()
                ->pluck('id');

        /**
         * Delete excluded that already exist.
         */
        $current->intersect($excluded)->each(function($id) use ($role) {
            \DB::table('permissions')
                ->where('ability_id', $id)
                ->where('entity_type', 'roles')
                ->where('entity_id', $role->getKey())
                ->delete();
        });


        /**
         * Remove what shouldn't be there.
         */
        $all->diff($excluded)->diff($current)->each(function($id) use ($role) {
            \DB::table('permissions')
                ->insert([
                    'entity_type' => 'roles',
                    'entity_id' => $role->getKey(),
                    'ability_id' => $id
                ]);
        });
    }

    public function getTenantAdministratorAbilitiesExcluded() : Collection {
        return $this->getTenantOwnerAbilitiesExcluded();
    }

    protected function roleTenantAdministrator() : void {
        $role = Role::firstOrCreate(['name' => 'tenant-administrator'], [
            'title' => 'Center Administrator'
        ]);

        // Get all ability IDs
        $all = $this->getAllAbilities();

        $excluded = $this->getTenantAdministratorAbilitiesExcluded();

        $current = \DB::table('abilities')
            ->whereNull('scope')
            ->whereNull('entity_id')
            ->whereIn('abilities.id',
                \DB::table('permissions')
                    ->where('permissions.entity_type', 'roles')
                    ->where('permissions.entity_id', $role->getKey())
                    ->select('permissions.ability_id')
            )
            ->select('id')
            ->get()
            ->pluck('id');

        /**
         * Delete excluded that already exist.
         */
        $current->intersect($excluded)->each(function($id) use ($role) {
            \DB::table('permissions')
                ->where('ability_id', $id)
                ->where('entity_type', 'roles')
                ->where('entity_id', $role->getKey())
                ->delete();
        });


        /**
         * Remove what shouldn't be there.
         */
        $all->diff($excluded)->diff($current)->each(function($id) use ($role) {
            \DB::table('permissions')
                ->insert([
                    'entity_type' => 'roles',
                    'entity_id' => $role->getKey(),
                    'ability_id' => $id
                ]);
        });
    }

    public function getTenantFacilitatorAbilitiesExcluded() : Collection {
        return once(function() {
            return $this->getTenantAdministratorAbilitiesExcluded()
                ->merge(\DB::table('abilities')
                    ->whereIn('entity_type', [
                        Plan::class,
                        PlanFeature::class,
                        PlanSubscription::class,
                        PlanSubscriptionUsage::class,
                        PlanSubscriptionFeature::class,
                        Role::class,
                        Tenant::class,
                        TenantUser::class,
                        TenantUserRole::class,
                        User::class
                    ])
                    ->select('id')
                    ->get()
                    ->pluck('id')
                )
                ->merge(\DB::table('abilities')
                    ->where('name', 'delete')
                    ->whereIn('entity_type', [
                        Client::class,
                        Collector::class,
                        Report::class,
                        Response::class,
                        Survey::class,
                        Tenant::class
                    ])->select('id')
                    ->get()
                    ->pluck('id')
                )
                ->unique();
        });
    }
    protected function roleTenantFacilitator() : void {
        $role = Role::firstOrCreate(['name' => 'tenant-facilitator'], [
            'title' => 'Center Facilitator'
        ]);

        // Get all ability IDs
        $all = $this->getAllAbilities();

        $excluded = $this->getTenantFacilitatorAbilitiesExcluded();

//dd($excluded);

        $current = \DB::table('abilities')
            ->whereNull('scope')
            ->whereNull('entity_id')
            ->whereIn('abilities.id',
                \DB::table('permissions')
                    ->where('permissions.entity_type', 'roles')
                    ->where('permissions.entity_id', $role->getKey())
                    ->select('permissions.ability_id')
            )
            ->select('id')
            ->get()
            ->pluck('id');

        /**
         * Delete excluded that already exist.
         */
        $current->intersect($excluded)->each(function($id) use ($role) {
            \DB::table('permissions')
                ->where('ability_id', $id)
                ->where('entity_type', 'roles')
                ->where('entity_id', $role->getKey())
                ->delete();
        });


        /**
         * Remove what shouldn't be there.
         */
        $all->diff($excluded)->diff($current)->each(function($id) use ($role) {
            \DB::table('permissions')
                ->insert([
                    'entity_type' => 'roles',
                    'entity_id' => $role->getKey(),
                    'ability_id' => $id
                ]);
        });
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

        $modelClasses = array_diff($modelClasses, [
            Ability::class,
            Role::class,
            Permission::class
        ]);

        return $modelClasses;
    }

    /**
     * Create permissions for a specific model
     */
    private function createModelPermissions(string $modelClass): void
    {
        // Get the model name without namespace
        $modelName = class_basename($modelClass);

        foreach ($this->standardPermissions as $permission) {
            if (\DB::table('abilities')
                ->where('entity_type', $modelClass)
                ->where('name', $permission)
                ->whereNull('scope')
                ->take(1)
                ->exists()) {
                continue;
            }
            ;

            /**
             * TODO: Holy cow, this needs simplified.
             */
            $title = Str::title(implode(' ', array_filter(
                array_merge(
                    preg_split('/(?=[A-Z])/',$permission),
                    preg_split('/(?=[A-Z])/',class_basename($modelClass)
                    )
                )
            )));

            \DB::table('abilities')
                ->insert([
                    'entity_type' => $modelClass,
                    'entity_id' => null,
                    'name' => $permission,
                    'scope' => null,
                    'title' => $title,
                    'created_at' => now()
                ]);
        }
    }
}

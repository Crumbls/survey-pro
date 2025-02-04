<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\Tenant;
use App\Models\User;
use App\Services\TenantService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;
use Crumbls\Infrastructure\Models\Node;
use Bouncer;
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

        $admin = Bouncer::role()->firstOrCreate([
            'name' => 'administrator',
            'title' => 'Administrator',
        ]);

        Bouncer::allow($admin)->to('access-filament');

//        Bouncer::allow($admin)->to('viewAny', Role::class);

        $user = User::firstOrCreate([
            'email' => 'chase@crumbls.com'
        ], [
            'name' => 'Chase Miller',
            'password' => Hash::make('password')
        ]);

        Bouncer::assign('administrator')->to($user);

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
        $methods = preg_grep('#^role[A-Z]#', get_class_methods(get_called_class()));
        foreach($methods as $method) {
            $this->$method();
        }


        dd(Role::all()->pluck('name'));

    }

    protected function roleTenantOwner() : void {
        $role = Role::firstOrCreate(['name' => 'tenant-owner'], [
            'title' => 'Center Owner'
        ]);
    }

    protected function roleTenantAdministrator() : void {
        $role = Role::firstOrCreate(['name' => 'tenant-administrator'], [
            'title' => 'Center Administrator'
        ]);
    }

    protected function roleTenantFacilitator() : void {
        $role = Role::firstOrCreate(['name' => 'tenant-facilitator'], [
            'title' => 'Center Facilitator'
        ]);
    }


    protected function roleClientFacilitator() : void {
        $role = Role::firstOrCreate(['name' => 'client-facilitator'], [
            'title' => 'Client Facilitator'
        ]);
    }

    protected function roleClientAdministrator() : void {
        $role = Role::firstOrCreate(['name' => 'client-administrator'], [
            'title' => 'Client Administrator'
        ]);
    }

    protected function roleClientOwner() : void {
        $role = Role::firstOrCreate(['name' => 'client-owner'], [
            'title' => 'Client Owner'
        ]);
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

    /**
     * Create permissions for a specific model
     */
    private function createModelPermissions(string $modelClass): void
    {
        // Get the model name without namespace
        $modelName = class_basename($modelClass);
        // Convert to kebab case for consistent naming
        $modelNameKebab = Str::kebab($modelName);
        $modelNameKebab = $modelClass;

        foreach ($this->standardPermissions as $permission) {
            // Check if permission already exists
            $exists = Bouncer::ability()
                ->where('name', $permission)
                ->where('entity_type', $modelClass)
                ->exists();

            if (!$exists) {
                Bouncer::ability()
                    ->create([
                        'name' => $permission,
                        'title' => ucfirst($permission) . ' ' . $modelName,
                        'entity_type' => $modelClass,
                    ]);

            }
        }
    }
}

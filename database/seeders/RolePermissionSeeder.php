<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
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

//        Bouncer::allow($admin)->to('viewAny', Role::class);

        $user = User::firstOrCreate([
            'email' => 'chase@crumbls.com'
        ], [
            'name' => 'Chase Miller',
            'password' => Hash::make('password')
        ]);

        Bouncer::assign('administrator')->to($user);

        $user = User::firstOrCreate([
            'email' => 'sumer@thebizxgroup.com'
        ], [
            'name' => 'Sumer',
            'password' => Hash::make('password')
        ]);

        Bouncer::assign('administrator')->to($user);

        Bouncer::allow($admin)->everything();
return;
        print_r(User::whereIs('administrator')->get());
//        print_r($admin->toArray());
        exit;

        print_r($models);
        exit;
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

                \Log::info("Created {$permission} permission for {$modelName}");
            }
        }
    }

    /**
     * Add custom permission to all models
     */
    public function addCustomPermission(string $permissionName): void
    {
        $models = $this->getModels();

        foreach ($models as $modelClass) {
            $exists = Ability::where('name', $permissionName)
                ->where('entity_type', $modelClass)
                ->exists();

            if (!$exists) {
                Bouncer::ability()
                    ->create([
                        'name' => $permissionName,
                        'title' => ucfirst($permissionName) . ' ' . class_basename($modelClass),
                        'entity_type' => $modelClass,
                    ]);

                \Log::info("Created {$permissionName} permission for {$modelClass}");
            }
        }
    }
}

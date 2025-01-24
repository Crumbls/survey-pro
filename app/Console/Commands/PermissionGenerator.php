<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use App\Models\Ability;
use Illuminate\Database\Eloquent\Relations\Relation;

class PermissionGenerator extends Command
{
    protected $signature = 'permissions:generate';
    protected $description = 'Generate standard CRUD permissions for all models';

    protected $standardPermissions = [
        'viewAny',
        'view',
        'create',
        'update',
        'delete',
        'restore',
        'forceDelete'
    ];

    public function handle()
    {
        $modelsPath = app_path('Models');
        $files = File::allFiles($modelsPath);

        $count = 0;

        foreach ($files as $file) {
            $className = 'App\\Models\\' . pathinfo($file->getFilename(), PATHINFO_FILENAME);

            if (!class_exists($className)) {
                continue;
            }

            $reflection = new \ReflectionClass($className);

            // Skip abstract classes, interfaces, and relation classes
            if ($reflection->isAbstract() ||
                $reflection->isInterface() ||
                $reflection->isSubclassOf(Relation::class) ||
                str_contains($reflection->getFileName(), 'Relations')) {
                continue;
            }

            $modelName = strtolower(class_basename($className));

            foreach ($this->standardPermissions as $permission) {
                $abilityName = "{$permission}-{$modelName}";
                foreach ($this->standardPermissions as $permission) {
                    $ability = Ability::firstOrCreate([
                        'name' => $permission,
                        'entity_type' => $className,
                    ], [
                        'title' => ucfirst($permission) . ' ' . class_basename($className),
                    ]);

                    if ($ability->wasRecentlyCreated) {
                        $count++;
                        $this->info("Created ability: {$permission} for {$className}");
                    }
                }
            }
        }

        $this->info("Created {$count} new permissions");
    }
}

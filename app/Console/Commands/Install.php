<?php

namespace App\Console\Commands;

use App\Models\Ability;
use App\Models\Permission;
use App\Models\Role;
use App\Models\Tenant;
use App\Models\TenantUserRole;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Services\SchemaService;

class Install extends Command
{
    protected $signature = 'app:install';
    protected $description = 'Install the application\'s defaults';

    public function handle(SchemaService $schemaService)
    {
        $defaults = collect($this->createAbilities());

        $role = Role::firstOrCreate([
            'title' => 'Super Admin'
        ]);

        /**
         * Work around to make sure the super admin can do anything.
         */

        foreach($defaults->diff($role->abilities->pluck('id')) as $abilityId) {
            Permission::create([
                'role_id' => $role->getKey(),
                'ability_id' => $abilityId
            ]);
        }

        $service = app(\App\Services\TenantService::class);

        /**
         * Clear out some data.
         */
        User::whereNotIn('users.id', \DB::table('users')
            ->where('email','like','%@crumbls.com')
//            ->orWhere('email','like','%@o2group.com')
  //          ->orWhere('email','like','sumer%')
            ->select('id')
        )
            ->get()
            ->each(function(User $record) {
//                $record->delete();
            });

        Tenant::whereNotIn('tenants.id', TenantUserRole::select('tenant_id'))
            ->where('created_at','<', now()->addMinutes(1))
            ->get()
            ->each(function($record) {
                $record->delete();
            });
        /**
         * Install test users.
         */
        foreach([
            [
                'email' => 'chase@crumbls.com',
                'name' => 'Chase',

            ],
                    [
                        'email' => 'jsitzman@o2group.com',
                        'name' => 'Jayme',
                    ],
                    [
                        'email' => 'sumersorensenbain@gmail.com',
                        'name' => 'Sumer'
                    ]
                ] as $dat) {

            $user = User::firstOrCreate([
                'email' => $dat['email']
            ], [
                'name' => $dat['name'],
                'password' => Hash::make('password')
            ]);

            $tenant = $service->getOrCreateDefault($user);


            if ($user->email == 'chase@crumbls.com') {
                TenantUserRole::firstOrCreate([
                    'user_id' => $user->getKey(),
                    'role_id' => $role->getKey()
                ]);
            }

        }
return;
        User::factory()->create();
    }

    public function createAbilities() : array {

        $defaults = [];

        $models = $this->getModels();

        foreach($models as $model) {
            $baseName = class_basename($model);

            $titleName = implode(' ',preg_split('/(?=[A-Z])/',$baseName));

            $ability = Ability::firstOrCreate([
                'name' => 'create',
                'entity_type' => $model
            ], [
                'title' => 'Create '.trim($titleName)
            ]);

            $defaults[] = $ability->getKey();

            $ability = Ability::firstOrCreate([
                'name' => 'viewAny',
                'entity_type' => $model
            ], [
                'title' => 'View Any '.trim($titleName)
            ]);

            $defaults[] = $ability->getKey();

            $ability = Ability::firstOrCreate([
                'name' => 'view',
                'entity_type' => $model
            ], [
                'title' => 'View '.trim($titleName)
            ]);

            $defaults[] = $ability->getKey();

            $ability = Ability::firstOrCreate([
                'name' => 'update',
                'entity_type' => $model
            ], [
                'title' => 'Update '.trim($titleName)
            ]);

            $defaults[] = $ability->getKey();

            $ability = Ability::firstOrCreate([
                'name' => 'delete',
                'entity_type' => $model
            ], [
                    'title' => 'Delete '.trim($titleName)
            ]);

            $defaults[] = $ability->getKey();

            if (in_array(SoftDeletes::class, class_uses($model))) {

                $ability = Ability::firstOrCreate([
                    'name' => 'restore',
                    'entity_type' => $model
                ], [
                    'title' => 'Restore ' . $titleName
                ]);

                $defaults[] = $ability->getKey();
            }
        }

        return $defaults;
    }

    public function getModels() : array {
        return once(function() {
            $modelsPath = app_path('Models');
            $files = File::allFiles($modelsPath);

            $ret = [];

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

                $ret[] = $className;
            }

            return $ret;
        });
    }
}

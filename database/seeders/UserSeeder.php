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

class UserSeeder extends Seeder
{
    public function run()
    {
        $service = app(\App\Services\TenantService::class);

        foreach([
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
//print_r($tenant->toArray());
continue;
            return;
            $service->createDefaultRolesPermissions($tenant);

            $role = \Silber\Bouncer\Database\Role::withoutGlobalScopes()->firstOrCreate([
                'name' => 'tenant-owner',
                'scope' => $tenant->getKey()
            ], [
                'title' => 'Center Owner',
            ]);

            \Silber\Bouncer\BouncerFacade::scope()->to($tenant->getKey());

            if (!$user->roles()->where('roles.id',$role->getKey())->exists()) {
                $role->assignTo($user);
            }


        }

    }

}

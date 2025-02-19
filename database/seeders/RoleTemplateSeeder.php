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
use App\Models\RoleTemplate;
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

class RoleTemplateSeeder extends Seeder
{
    protected $roleTemplates = [
        [
            'name' => 'super-admin',
            'display_name' => 'Administrator',
            'description' => 'Full system access with all permissions',
            'is_global' => true,
            'default_abilities' => [
                '*' // Special flag for all permissions
            ],
        ],
        [
            'name' => 'tenant-owner',
            'display_name' => 'Center Owner',
            'description' => 'Owner of the tenant instance',
            'is_global' => false,
            'default_abilities' => [
                'App\\Models\\Client,*',
                'App\\Models\\Collector,*',
                'App\\Models\\Product,*',
                'App\\Models\\Report,*',
                'App\\Models\\Response,*',
                'App\\Models\\Survey,*',
                'App\\Models\\User,create',
                'App\\Models\\User,update',
                'App\\Models\\User,delete',
            ],
        ],
        [
            'name' => 'tenant-member',
            'display_name' => 'Center Member',
            'description' => 'Standard member of the tenant',
            'is_global' => false,
            'default_abilities' => [
                'App\\Models\\Client,*',
                'App\\Models\\Collector,*',
                'App\\Models\\Product,*',
                'App\\Models\\Report,*',
                'App\\Models\\Response,*',
                'App\\Models\\Survey,*'
            ],
        ]
    ];

    public function run()
    {
        foreach ($this->roleTemplates as $template) {
            $record = RoleTemplate::withTrashed()
                ->firstOrCreate([
                    'is_global' => $template['is_global'],
                    'name' => $template['name'],
                ], $template);
        }

    }
}

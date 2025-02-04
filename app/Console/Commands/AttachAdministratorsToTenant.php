<?php

namespace App\Console\Commands;

use App\Models\Ability;
use App\Models\Role;
use App\Models\Tenant;
use App\Models\User;
use App\Providers\TestPipeline;
use App\Services\SchemaService;
use Illuminate\Console\Command;

class AttachAdministratorsToTenant extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'management:attach-administrators';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        return;
        $tenants = \App\Models\Tenant::pluck('id');

        $users = \App\Models\User::where('email', 'LIKE', '%@crumbls.com')
            ->orWhere('email', 'LIKE', '%@thebizxgroup.com')
            ->orWhere('email', 'LIKE', '%@o2group.com')
            ->select('users.id')
            ->addSelect($tenants->map(function($tenantId) {
                return \DB::raw("MAX(CASE WHEN tenant_user_role.tenant_id = $tenantId THEN 1 ELSE 0 END) as tenant_{$tenantId}_exists");
            })->toArray())
            ->leftJoin('tenant_user_role', 'users.id', '=', 'tenant_user_role.user_id')
            ->groupBy('users.id')
            ->havingRaw('CONCAT(' . $tenants->map(function($id) { return "tenant_{$id}_exists"; })->implode(',') . ') != ' . str_repeat('1', $tenants->count()))
            ->get();

        $role = Role::firstOrCreate(
            [
                'name' => 'administrator'
            ],
            [
                'title' => 'Administrator',
                'level' => 100
            ]
        );

        foreach($users as $user) {
            $user = $user->toArray();
            $parse = array_keys(array_filter(array_intersect_key(
                $user,
                array_flip(preg_grep('#^tenant\_\d{1,}\_exists$#',array_keys($user)))),
                function($exists) {
                    return !$exists;
                }));
            foreach($parse as $k) {
                dd(__LINE__);

                \App\Models\TenantUserRole::create([
                    'user_id' => $user['id'],
                    'tenant_id' => substr($k, 7, -7),
                    'role_id' => $role->getKey()
                ]);
            }
        }
        print_r($users);
    }
}

<?php

namespace App\Console\Commands;

use App\Models\Tenant;
use App\Models\User;
use App\Services\TenantService;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use App\Services\SchemaService;

class PatchOwner extends Command
{
    protected $signature = 'patch:owner';
    protected $description = 'Patch owner for tenants';

    public function handle()
    {
        $temp = \DB::table('roles')
            ->whereNotNull('scope')
            ->where('name','tenant-owner')
            ->whereIn('scope', \DB::table('tenants')->select('id'))
//            ->select('id')
  //          ->whereNotIn('')
            ->whereNotIn('id',
                \DB::table('assigned_roles')
                    ->select('role_id')
            )
            ->whereIn('scope', \DB::table('tenant_user')->select('tenant_id'))
            ->get()
            ->each(function ($role) {
                /**
                 * Move first user to center owner
                 */
                $user = \DB::table('tenant_user')
                    ->where('tenant_id', $role->scope)
                    ->whereNotIn('tenant_user.user_id',
                        \DB::table('assigned_roles')
                            ->where('assigned_roles.entity_type', User::class)
                            ->whereIn('assigned_roles.role_id',
                                \DB::table('roles')
                                    ->where('roles.scope', $role->scope)
                                    ->select('id')
                            )
                            ->select('assigned_roles.entity_id')
                    )
                    ->select('id')
                    ->take(1)
                    ->first();

                if ($user) {
                    \DB::table('assigned_roles')
                        ->insert([
                            'role_id' => $role->id,
                            'entity_id' => $user->id,
                            'entity_type' => User::class
                        ]);
                    $this->info('patched.');
                    return;
                }

                if (!$user) {
                    dd($user);
                }

                if (!$user) {
                    return;
                }

                dd($role, $user);

            });
    }
}

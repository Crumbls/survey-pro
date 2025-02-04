<?php

namespace App\Console\Commands;

use App\Models\Tenant;
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
            ->whereIn('id', \DB::table('tenant_user')->select('tenant_id'))
            ->get();

        dd($temp);
    }
}

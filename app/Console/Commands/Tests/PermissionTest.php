<?php

namespace App\Console\Commands\Tests;

use App\Models\Tenant;
use App\Services\TenantService;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use App\Services\SchemaService;
use Ramsey\Uuid\Uuid;

class PermissionTest extends Command
{
    protected $signature = 'test:permission';
    protected $description = 'Test our UUID generator.';



    public function handle()
    {
       $tenant = Tenant::all();

       $service = app(TenantService::class);

       $tenant->each(function ($tenant) use ($service) {
           $this->info($tenant->name);
           $service->createDefaultRolesPermissions($tenant);
       });
    }
}

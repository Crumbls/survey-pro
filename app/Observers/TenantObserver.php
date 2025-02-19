<?php

namespace App\Observers;

use App\Models\Role;
use App\Models\Tenant;
use App\Models\TenantUserRole;

class TenantObserver
{
    /**
     * Handle the Tenant "created" event.
     */
    public function created(Tenant $record): void
    {
        /**
         * Create any clients
         */
        $clientName = $record->name;

        if (preg_match('#^(.*?\'s) Organization$#', $record->name, $temp)) {
            $clientName = $temp[1].' '.trans('clients.singular');
        }

        $client = $record->clients()->create([
//            'user_id' => $record->user_id,
            'name' => $clientName
        ]);

        $service = app(\App\Services\TenantService::class);

        $service->createDefaultRolesPermissions($record);

    }

    public function deleting(Tenant $record): void {
        TenantUserRole::where('tenant_id',  $record->getKey())
            ->get()
            ->each(function ($pivot) {
                $pivot->delete();
            });

        Role::where('tenant_id', $record->getKey())
            ->get()
            ->each(function ($role) {
                $role->delete();
            });

    }

}

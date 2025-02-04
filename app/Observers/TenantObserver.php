<?php

namespace App\Observers;

use App\Models\Tenant;

class TenantObserver
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

}

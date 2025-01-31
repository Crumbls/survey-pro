<?php

namespace App\Services;

use App\Models\Client;
use App\Models\Tenant;

class ClientService
{
    public function getOrCreateDefault(Tenant $tenant): Client
    {
        if ($existingClient = $tenant->clients()->first()) {
            return $existingClient;
        }

        return $tenant->clients()->create([
            'name' => $this->generateDefaultClientName($tenant),
        ]);
    }

    private function generateDefaultClientName(Tenant $tenant): string
    {
        if ($tenant->user) {
            return $tenant->user->name.'\' Client';
        }
        return 'Your Client';
    }
}

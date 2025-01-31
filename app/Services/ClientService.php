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

        // Create new tenant with default naming convention
        $client = Client::create([
            'name' => $this->generateDefaultClientName($tenant),
            'tenant_id' => $tenant->getKey()
        ]);

        return $client;
    }

    private function generateDefaultClientName(Tenant $tenant): string
    {
        if ($tenant->user) {
            return $tenant->user->name.'\' Client';
        }
        return 'Your Client';
    }
}

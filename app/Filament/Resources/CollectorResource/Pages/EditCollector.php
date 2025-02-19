<?php

namespace App\Filament\Resources\CollectorResource\Pages;

use App\Filament\Resources\CollectorResource;
use App\Models\Client;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCollector extends EditRecord
{
    protected static string $resource = CollectorResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }


    protected function fillForm(): void
    {
        parent::fillForm();

        /**
         * Get the client id.
         */
        $clientId = array_key_exists('client_id', $this->data) && $this->data['client_id'] ? $this->data['client_id'] : null;

        $tenantId = null;

        if ($clientId && $client = Client::find($clientId)) {
            $tenantId = $client->tenant_id;
        }

        $this->data['tenant_id'] = $tenantId;

        // Set the initial state of the tenant_id field
        $this->form->fill($this->data);
    }

}

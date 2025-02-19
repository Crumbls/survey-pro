<?php

namespace App\Filament\Resources\CollectorResource\Pages;

use App\Filament\Resources\CollectorResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateCollector extends CreateRecord
{
    protected static string $resource = CollectorResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['type'] = 'url';
        $data['status'] = 'open';
        $data['configuration'] = [];
        $data['name'] = $data['unique_code'];

        return $data;
    }
}

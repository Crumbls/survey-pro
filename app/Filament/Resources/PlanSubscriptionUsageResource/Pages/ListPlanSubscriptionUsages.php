<?php

namespace App\Filament\Resources\PlanSubscriptionUsageResource\Pages;

use App\Filament\Resources\PlanSubscriptionUsageResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPlanSubscriptionUsages extends ListRecords
{
    protected static string $resource = PlanSubscriptionUsageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}

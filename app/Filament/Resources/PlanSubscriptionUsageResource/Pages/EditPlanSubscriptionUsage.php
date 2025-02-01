<?php

namespace App\Filament\Resources\PlanSubscriptionUsageResource\Pages;

use App\Filament\Resources\PlanSubscriptionUsageResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPlanSubscriptionUsage extends EditRecord
{
    protected static string $resource = PlanSubscriptionUsageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}

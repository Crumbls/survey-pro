<?php

namespace App\Filament\Resources\PlanFeatureResource\Pages;

use App\Filament\Resources\PlanFeatureResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPlanFeature extends EditRecord
{
    protected static string $resource = PlanFeatureResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}

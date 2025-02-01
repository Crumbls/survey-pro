<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PlanSubscriptionUsageResource\Pages;
use App\Filament\Resources\PlanSubscriptionUsageResource\RelationManagers;
use App\Models\PlanSubscriptionUsage;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PlanSubscriptionUsageResource extends Resource
{
    protected static ?string $model = PlanSubscriptionUsage::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    public static function getNavigationGroup(): ?string {
        return PlanResource::getNavigationGroup();
    }

    public static function getNavigationSort(): int
    {
        return PlanFeatureResource::getNavigationSort() +  10;
    }


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPlanSubscriptionUsages::route('/'),
            'create' => Pages\CreatePlanSubscriptionUsage::route('/create'),
            'edit' => Pages\EditPlanSubscriptionUsage::route('/{record}/edit'),
        ];
    }
}

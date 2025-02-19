<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PermissionResource\Pages;
use App\Filament\Resources\PermissionResource\RelationManagers;
use App\Models\Permission;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PermissionResource extends Resource
{
    protected static ?string $model = Permission::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function getNavigationGroup(): ?string {
        return RoleResource::getNavigationGroup();
    }

    public static function getNavigationSort(): int
    {
        return AbilityResource::getNavigationSort() +  10;
    }


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('ability_id')
                    ->relationship('ability', 'name')
                    ->required(),
                Forms\Components\Select::make('role_id')
                    ->relationship('role', 'name')
                    ->required(),
                Forms\Components\Toggle::make('forbidden')
                     ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('ability_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('entity_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('entity_type')
                    ->searchable(),
                Tables\Columns\IconColumn::make('forbidden')
                    ->boolean(),
                Tables\Columns\TextColumn::make('scope')
                    ->numeric()
                    ->sortable(),
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
            'index' => Pages\ListPermissions::route('/'),
            'create' => Pages\CreatePermission::route('/create'),
            'edit' => Pages\EditPermission::route('/{record}/edit'),
        ];
    }
}

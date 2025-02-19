<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AbilityResource\Pages;
use App\Filament\Resources\AbilityResource\RelationManagers;
use App\Models\Ability;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class AbilityResource extends Resource
{
    protected static ?string $model = Ability::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function getNavigationGroup(): ?string {
        return RoleResource::getNavigationGroup();
    }

    public static function getNavigationSort(): int
    {
        return RoleResource::getNavigationSort() +  10;
    }


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required(),
                Forms\Components\TextInput::make('title'),
                Forms\Components\TextInput::make('entity_id')
                    ->numeric(),
                Forms\Components\TextInput::make('entity_type'),
                Forms\Components\Toggle::make('only_owned')
                    ->required(),
                Forms\Components\Textarea::make('options')
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('title')
                    ->searchable(),
                Tables\Columns\TextColumn::make('entity_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('entity_type')
                    ->searchable(),
                Tables\Columns\IconColumn::make('only_owned')
                    ->boolean(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
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
            'index' => Pages\ListAbilities::route('/'),
            'create' => Pages\CreateAbility::route('/create'),
            'edit' => Pages\EditAbility::route('/{record}/edit'),
        ];
    }
}

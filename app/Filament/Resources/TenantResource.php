<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TenantResource\Pages;
use App\Filament\Resources\TenantResource\RelationManagers;
use App\Models\Tenant;
use Filament\Forms;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TenantResource extends Resource
{
    protected static ?string $model = Tenant::class;

    public static function getNavigationGroup(): ?string {
        return __('commons.general');
    }

    public static function getNavigationLabel(): string
    {
        return trans('tenants.plural');
    }

    public static function getNavigationSort(): int
    {
        return 10;
    }

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Grid::make(1)->schema([
                    Section::make('Basic Information')
                        ->schema([
                            TextInput::make('name')
                                ->required()
                                ->maxLength(255),
                            SpatieMediaLibraryFileUpload::make('logo')
                                ->collection('logo')
                                ->visibility('public')
                                ->disk('public')
                                ->openable()
                        ]),

                    Section::make('Color Scheme')
                        ->description('Customize your organization\'s colors. Upload a logo to automatically extract a color scheme.')
                        ->schema([
                            Grid::make(3)
                                ->schema([
                                    ColorPicker::make('primary_color')
                                        ->label('Primary Color')
                                        ->required(),

                                    ColorPicker::make('secondary_color')
                                        ->label('Secondary Color')
                                        ->required(),

                                    ColorPicker::make('accent_color')
                                        ->label('Accent Color')
                                        ->required(),
                                ]),
                        ]),
                ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('uuid')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('deleted_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),
                ])
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
            RelationManagers\UsersRelationManager::make(),
            RelationManagers\RolesRelationManager::make(),
            RelationManagers\SubscriptionsRelationManager::make()
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTenants::route('/'),
            'create' => Pages\CreateTenant::route('/create'),
            'edit' => Pages\EditTenant::route('/{record}/edit'),
        ];
    }
}

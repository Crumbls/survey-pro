<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ClientResource\Pages;
use App\Filament\Resources\ClientResource\RelationManagers;
use App\Models\Client;
use Filament\Actions\DeleteAction;
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

class ClientResource extends Resource
{
    protected static ?string $model = Client::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    public static function getNavigationGroup(): ?string {
        return TenantResource::getNavigationGroup();
    }

    public static function getNavigationSort(): int
    {
        return TenantResource::getNavigationSort() + 10;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Grid::make(1)->schema([
                    Section::make('Basic Information')
                        ->schema([
                            Forms\Components\Select::make('tenant_id')
                                ->label(trans('tenants.singular'))
                                ->relationship('tenant', 'name')
                                ->required()
                                ->columnSpanFull(),
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
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('tenant.name')
                    ->label(trans('tenants.singular'))
                    ->numeric()
                    ->sortable(),
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
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('name')
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make()
                    ->action(function(Client $record) {
                        $record->delete();
//                        dd($record);
                    })
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
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListClients::route('/'),
            'create' => Pages\CreateClient::route('/create'),
            'edit' => Pages\EditClient::route('/{record}/edit'),
        ];
    }
}

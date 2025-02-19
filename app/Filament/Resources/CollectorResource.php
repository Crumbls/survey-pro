<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CollectorResource\Pages;
use App\Filament\Resources\CollectorResource\RelationManagers;
use App\Models\Client;
use App\Models\Collector;
use App\Models\Survey;
use App\Models\Tenant;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CollectorResource extends Resource
{
    protected static ?string $model = Collector::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function getNavigationGroup(): ?string {
        return TenantResource::getNavigationGroup();
    }


    public static function getNavigationSort(): int
    {
        return SurveyResource::getNavigationSort() + 10;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('tenant_id')
                    ->label(__('tenants.singular'))
                    ->options(function () {
                        return Tenant::whereIn('tenants.id',
                                Client::select('tenant_id')
                                    ->whereIn('clients.id', Survey::select('client_id'))
                            )
                            ->pluck('name', 'id');
                    })
                    ->live()  // Makes the field reactive
                    ->afterStateUpdated(function (Set $set) {
                        // Clear the dependent field when parent changes
                        $set('client_id', null);
                        $set('survey_id', null);
                        $set('collector_ids', null);
                    })
                    ->required(),

                Select::make('client_id')
                    ->label(__('clients.singular'))
                    ->options(function (Get $get) {
                        $tenantId = $get('tenant_id');
                        if (!$tenantId) {
                            return [];
                        }
                        return Client::where('tenant_id', $tenantId)
                            ->whereIn('clients.id', Survey::select('client_id'))
                            ->orderBy('clients.name','asc')
                            ->pluck('name','id');
                    })
                    ->live()  // Makes the field reactive
                    ->afterStateUpdated(function (Set $set) {
                        // Clear the dependent field when parent changes
                        $set('survey_id', null);
                        $set('collector_ids', null);
                    })
                    ->required(),
                Select::make('survey_id')
                    ->label('Survey')
                    ->options(function (Get $get) {
                        $clientId = $get('client_id');
                        if (!$clientId) {
                            return [];
                        }
                        return Survey::query()
                            ->where('client_id', $clientId)
                            ->get()
                            ->pluck('title', 'id');
                    })
                    ->live()  // Makes the field reactive
                    ->afterStateUpdated(function (Set $set) {
                        // Clear the dependent field when parent changes
                        $set('collector_ids', null);
                    })
                    ->required(),
                TextInput::make('unique_code')
                    ->label('Reference')
                    ->prefix('/r/')
                    ->required()
                    ->maxLength(250)
                    ->regex('/^[a-zA-Z0-9\-]+$/')
                    ->unique(
                        'collectors',
                        'unique_code',
                        ignoreRecord: true,
                        modifyRuleUsing: fn ($rule, $state) => $rule->where('unique_code', $state)
                    )
                    ->live(onBlur: true)
                    ->afterStateUpdated(function ($state, callable $set) {
                        if ($state) {
                            $processed = preg_replace('/\s+/', '-', trim($state)); // Replace spaces with hyphens
                            $processed = preg_replace('/\-+/', '-', $processed);   // Remove duplicate hyphens
                            $processed = preg_replace('/^-+|-+$/', '', $processed); // Remove leading/trailing hyphens
                            $processed = preg_replace('/[^a-zA-Z0-9\-]/', '', $processed); // Remove invalid chars
                            $set('unique_code', $processed);
                        }
                    }),

                TextInput::make('goal')
                    ->required()
                    ->numeric()
                    ->minValue(0)
                    ->maxValue(1000000)
                    ->integer()
                    ->label('Target Response Count')

            ]);

    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('survey_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('type')
                    ->searchable(),
                Tables\Columns\TextColumn::make('status')
                    ->searchable(),
                Tables\Columns\TextColumn::make('unique_code')
                    ->searchable(),
                Tables\Columns\TextColumn::make('expires_at')
                    ->dateTime()
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
                Tables\Columns\TextColumn::make('client.name')
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
            'index' => Pages\ListCollectors::route('/'),
            'create' => Pages\CreateCollector::route('/create'),
            'edit' => Pages\EditCollector::route('/{record}/edit'),
        ];
    }
}

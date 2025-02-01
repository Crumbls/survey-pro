<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PlanResource\Pages;
use App\Filament\Resources\PlanResource\RelationManagers;
use App\Models\Plan;
use App\Models\Tenant;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PlanResource extends Resource
{
    protected static ?string $model = Plan::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    public static function getNavigationGroup(): ?string {
        return __('subscriptions.plural');
    }

    public static function getNavigationSort(): int
    {
        return TenantResource::getNavigationSort() + 100;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('uuid')
                    ->label('UUID')
                    ->required(),
                Forms\Components\TextInput::make('name')
                    ->required(),
                Forms\Components\Textarea::make('description')
                    ->columnSpanFull(),
                Forms\Components\Toggle::make('is_active')
                    ->required(),
                Forms\Components\TextInput::make('price')
                    ->required()
                    ->numeric()
                    ->default(0.00)
                    ->prefix('$'),
                Forms\Components\TextInput::make('signup_fee')
                    ->required()
                    ->numeric()
                    ->default(0.00),
                Forms\Components\TextInput::make('currency')
                    ->required(),
                Forms\Components\TextInput::make('trial_period')
                    ->required()
                    ->numeric()
                    ->default(0),
                Forms\Components\TextInput::make('trial_interval')
                    ->required(),
                Forms\Components\TextInput::make('invoice_period')
                    ->required()
                    ->numeric()
                    ->default(0),
                Forms\Components\TextInput::make('invoice_interval')
                    ->required(),
                Forms\Components\TextInput::make('grace_period')
                    ->required()
                    ->numeric()
                    ->default(0),
                Forms\Components\TextInput::make('grace_interval')
                    ->required(),
                Forms\Components\TextInput::make('prorate_day')
                    ->numeric(),
                Forms\Components\TextInput::make('prorate_period')
                    ->numeric(),
                Forms\Components\TextInput::make('prorate_extend_due')
                    ->numeric(),
                Forms\Components\TextInput::make('active_subscribers_limit')
                    ->numeric(),
                Forms\Components\TextInput::make('sort_order')
                    ->required()
                    ->numeric()
                    ->default(0),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('uuid')
                    ->label('UUID')
                    ->searchable(),
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean(),
                Tables\Columns\TextColumn::make('price')
                    ->money()
                    ->sortable(),
                Tables\Columns\TextColumn::make('signup_fee')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('currency')
                    ->searchable(),
                Tables\Columns\TextColumn::make('trial_period')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('trial_interval')
                    ->searchable(),
                Tables\Columns\TextColumn::make('invoice_period')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('invoice_interval')
                    ->searchable(),
                Tables\Columns\TextColumn::make('grace_period')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('grace_interval')
                    ->searchable(),
                Tables\Columns\TextColumn::make('prorate_day')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('prorate_period')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('prorate_extend_due')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('active_subscribers_limit')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('sort_order')
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
            'index' => Pages\ListPlans::route('/'),
            'create' => Pages\CreatePlan::route('/create'),
            'edit' => Pages\EditPlan::route('/{record}/edit'),
        ];
    }
}

<?php

namespace App\Filament\Resources\TenantResource\RelationManagers;

use App\Models\Role;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class UsersRelationManager extends RelationManager
{
    protected static string $relationship = 'users';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Select::make('role')
                ->relationship('role')
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                Tables\Columns\TextColumn::make('name'),
                Tables\Columns\SelectColumn::make('pivot.role.name')
                    ->options(function() {
                        return once(function() { return Role::all()->pluck('name', 'id')->toArray(); });
                    })
                    ->selectablePlaceholder(false)
                    ->state(function($record) {
                        return $record->pivot->role_id;
                    })
                    ->beforeStateUpdated(function ($record, $state) {
                        $record->pivot->update(['role_id' => $state]);
                        return false;
                    })
                ,
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}

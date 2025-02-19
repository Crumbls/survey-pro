<?php

namespace App\Filament\Resources\TenantResource\RelationManagers;

use App\Models\Role;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\Auth;

class RolesRelationManager extends RelationManager
{
    protected static string $relationship = 'roles';

    protected static ?string $recordTitleAttribute = 'title';

    public function getModel() {
        return Role::class;
    }
    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return __('roles.plural');
    }

    public static function getModelLabel(): string
    {
        return __('roles.singular');
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([

            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('title')
            ->columns([
                Tables\Columns\TextColumn::make('title'),
            ])
            ->filters([

            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()

            ])
            ->actions([
//                Tables\Actions\DetachAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DetachBulkAction::make(),
                ]),
            ])
            ->query(function (Builder $query) {
                $parent = $this->getOwnerRecord();
                return $parent->roles()->getQuery();
            });
    }

    protected function getRoles()
    {
        return once(function() {
            return Role::withoutGlobalScopes()->where('scope', $this->getOwnerRecord()->getKey())
                ->orderBy('title','asc')
                ->get();
        });
    }
}

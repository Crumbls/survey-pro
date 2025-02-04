<?php

namespace App\Filament\Resources\UserResource\RelationManagers;

use App\Models\Role;
use App\Models\Tenant;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class TenantsRelationManager extends RelationManager
{
    protected static string $relationship = 'tenants';

    protected static ?string $recordTitleAttribute = 'name';


    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return __('tenants.plural');
    }

    public static function getModelLabel(): string
    {
        return __('filament/resources/user.relationships.tenants.label');
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('name')
                    ->relationship('tenant', 'name')
                    ->preload()
                    ->required(),
                Forms\Components\Select::make('role_id')
                    ->label('Role')
                    ->options(function () {
//                        dd($this->record);
///                        \Silber\Bouncer\Database\Role::withoutGlobalScope
                        $roles = Role::query();

                        if (Auth::user()->email !== 'chase@crumbls.com') {
                            $roles->where('name', '!=', 'administrator');
                        }

                        return $roles->pluck('name', 'id');
                    })
                    ->required(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                Tables\Columns\TextColumn::make('name'),
                Tables\Columns\SelectColumn::make('role_id')
                    ->options(function (Tenant $record) {
                        return once(function() use ($record){
                            dd($record->getRoles());
                            dd($record->getKey(), $record);
                           return \Silber\Bouncer\Database\Role::withoutGlobalSCopes()
                               ->where('scope', $record->getKey())
                               ->orderBy('title','asc')
                               ->get()
                               ->pluck('title', 'id');
                        });
                    })
                    ->afterStateUpdated(function ($record, $state) {
                        $this->ownerRecord->tenants()->updateExistingPivot(
                            $record->id,
                            ['role_id' => $state]
                        );
                    }),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('role')
                    ->options(function () {
                        $roles = Role::query();

                        if (Auth::user()->email !== 'chase@crumbls.com') {
                            $roles->where('name', '!=', 'Administrator');
                        }

                        return $roles->pluck('name', 'id');
                    })
                    ->query(function (Builder $query, array $data) {
                        if (!empty($data['value'])) {
                            $query->wherePivot('role_id', $data['value']);
                        }
                    }),
            ])
            ->headerActions([
                Tables\Actions\AttachAction::make()
                    ->preloadRecordSelect()
                    ->recordSelectOptionsQuery(function (Builder $query) {
                        // Exclude tenants that are already attached
                        return $query->whereDoesntHave('users', function (Builder $query) {
                            $query->where('users.id', $this->ownerRecord->id);
                        });
                    })
                    ->form(fn (Tables\Actions\AttachAction $action): array => [
                        $action->getRecordSelect(),
                        Forms\Components\Select::make('role_id')
                            ->label('Role')
                            ->options(function () {
                                $roles = Role::query();

                                if (Auth::user()->email !== 'chase@crumbls.com') {
                                    $roles->where('name', '!=', 'Administrator');
                                }

                                return $roles->pluck('name', 'id');
                            })
                            ->required(),
                    ]),
            ])
            ->actions([
                Tables\Actions\DetachAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DetachBulkAction::make(),
                ]),
            ]);
    }
}

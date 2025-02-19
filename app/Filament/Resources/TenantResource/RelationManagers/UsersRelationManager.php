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

class UsersRelationManager extends RelationManager
{
    protected static string $relationship = 'users';

    protected static ?string $recordTitleAttribute = 'name';

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return __('users.plural');
    }

    public static function getModelLabel(): string
    {
        return __('users.singular');
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('name')
                    ->relationship('user', 'name')
                    ->required(),
                Forms\Components\Select::make('role_id')
                    ->label('Role')
                    ->options(function () {
                        return $this->getRoles()->pluck('title', 'id');
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
                    ->label('roles.singular')
                    ->options(function () {
                        return $this->getRoles()->pluck('title', 'id');
                    })
                    ->afterStateUpdated(function ($record, $state) {
                        $this->ownerRecord->users()->updateExistingPivot(
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
                        // Exclude users that are already attached
                        return $query->whereDoesntHave('tenants', function (Builder $query) {
                            $query->where('tenants.id', $this->ownerRecord->id);
                        });
                    })
                    ->form(fn (Tables\Actions\AttachAction $action): array => [
                        $action->getRecordSelect(),
                        Forms\Components\Select::make('role_id')
                            ->label('Role')
                            ->options(function () {
                                return $this->getRoles()->pluck('title','id');
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

    protected function getRoles()
    {
        return once(function() {
            return Role::withoutGlobalScopes()
                ->where('scope', $this->getOwnerRecord()->getKey())
                ->orderBy('title','asc')
                ->get();
        });
    }
}

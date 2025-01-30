<?php

namespace App\Filament\Resources\RoleResource\RelationManagers;

use App\Models\Ability;
use App\Models\Permission;
use App\Models\Role;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class AbilitiesRelationManager extends RelationManager
{
    protected static string $relationship = 'abilities';

    protected static ?string $recordTitleAttribute = 'title';

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return __('abilities.plural');
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('abilities')
                    ->relationship('abilities', 'title')
                    ->multiple()
                    ->preload()
                    ->searchable(),
                Forms\Components\Toggle::make('forbidden')
                    ->default(false)
                    ->reactive(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('title')
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\IconColumn::make('permissions')
                    ->label('Status')
                    ->getStateUsing(function (Model $record) {
                        $permission = Permission::where([
                            'ability_id' => $record->id,
                            'entity_type' => Role::class,
                            'entity_id' => $this->ownerRecord->id,
                        ])->first();

                        return $permission?->forbidden ? 'forbidden' : 'granted';
                    })
                    ->icon(fn (string $state): string => match ($state) {
                        'granted' => 'heroicon-o-check-circle',
                        'forbidden' => 'heroicon-o-x-circle',
                        default => 'heroicon-o-minus-circle',
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'granted' => 'success',
                        'forbidden' => 'danger',
                        default => 'warning',
                    }),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'granted' => 'Granted',
                        'forbidden' => 'Forbidden',
                    ])
                    ->query(function (Builder $query, array $data) {
                        if (!$data['value']) {
                            return $query;
                        }

                        return $query->whereHas('roles', function ($query) use ($data) {
                            $query->where('roles.id', $this->ownerRecord->id)
                                ->whereHas('permissions', function ($query) use ($data) {
                                    $query->where('forbidden', $data['value'] === 'forbidden');
                                });
                        });
                    }),
            ])
            ->headerActions([
                Tables\Actions\AttachAction::make()
                    ->preloadRecordSelect()
                    ->form(fn (Tables\Actions\AttachAction $action): array => [
                        $action->getRecordSelect(),
                        Forms\Components\Toggle::make('forbidden')
                            ->label('Deny Access')
                            ->default(false),
                    ])
                    ->after(function ($data, $record) {
                        // Create or update permission
                        Permission::updateOrCreate(
                            [
                                'ability_id' => $record->id,
                                'entity_type' => Role::class,
                                'entity_id' => $this->ownerRecord->id,
                            ],
                            [
                                'forbidden' => $data['forbidden'],
                            ]
                        );
                    }),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([

                Tables\Actions\Action::make('toggle_permission')
                    ->icon(function (Model $record) {
                        $permission = Permission::where([
                            'ability_id' => $record->id,
                            'entity_type' => Role::class,
                            'entity_id' => $this->ownerRecord->id,
                        ])->first();

                        return $permission?->forbidden ? 'heroicon-o-check-circle' : 'heroicon-o-x-circle';
                    })
                    ->color(function (Model $record) {
                        $permission = Permission::where([
                            'ability_id' => $record->id,
                            'entity_type' => Role::class,
                            'entity_id' => $this->ownerRecord->id,
                        ])->first();

                        return $permission?->forbidden ? 'success' : 'danger';
                    })
                    ->action(function (Model $record) {
                        $permission = Permission::updateOrCreate(
                            [
                                'ability_id' => $record->id,
                                'entity_type' => Role::class,
                                'entity_id' => $this->ownerRecord->id,
                            ],
                            [
                                'forbidden' => DB::raw('NOT forbidden'),
                            ]
                        );
                    })
                    ->requiresConfirmation(),
                Tables\Actions\DetachAction::make()
                    ->after(function ($record) {
                        // Clean up any permissions when detaching
                        Permission::where([
                            'ability_id' => $record->id,
                            'entity_type' => Role::class,
                            'entity_id' => $this->ownerRecord->id,
                        ])->delete();
                    }),
                ])

            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DetachBulkAction::make()
                        ->after(function (array $records) {
                            // Clean up permissions for all detached records
                            foreach ($records as $record) {
                                Permission::where([
                                    'ability_id' => $record['id'],
                                    'entity_type' => Role::class,
                                    'entity_id' => $this->ownerRecord->id,
                                ])->delete();
                            }
                        }),
                ]),
            ]);
    }
}

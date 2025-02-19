<?php

namespace App\Filament\Resources\RoleResource\Pages;

use App\Filament\Resources\RoleResource;
use App\Models\Permission;
use Filament\Actions;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;
use App\Models\Ability;
use App\Models\Role;
use Illuminate\Validation\Rule;

class EditRole extends EditRecord
{
    protected static string $resource = RoleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function getAbilitiesQuery()
    {
        return Ability::withoutGlobalScopes();
    }

    protected function getScopedUniqueRule()
    {
        $role = $this->getRecord();

        $scope = $role->tenant_id;

        return Rule::unique('roles', 'name')
            ->where(function ($query) use ($scope) {
                return $query->where('tenant_id', $scope);
            })
            ->ignore($role->getKey());
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Role Details')
                    ->description('Basic information about the role')
                    ->schema([
                        TextInput::make('name')
                            ->required()
                            ->maxLength(255)
                            ->rules([
                                fn () => $this->getScopedUniqueRule()
                            ]),
                        TextInput::make('title')
                            ->maxLength(255)
                            ->hint('A human-readable title for this role'),
                        /*
                        TextInput::make('scope')
                            ->numeric()
                            ->hint('Leave empty for global roles'),
                        */
                    ])
                    ->columns(2),

                Section::make('Abilities')
                    ->description('Select the abilities for this role')
                    ->schema([
                        Select::make('abilities')
                            ->multiple()
                            ->preload()
                            ->searchable()
                            ->options(function () {
                                return \App\Models\Ability::orderBy('name', 'asc')
                                    ->get()
                                    ->map(function ($row) {
                                        $row->title = $row->title ? $row->title : $row->name;
                                        return $row;
                                    })
                                    ->pluck('title','id');
                            })
                            ->getSearchResultsUsing(function (string $search) {
                                return \DB::table('abilities')
                                        ->where('name', 'like', "%{$search}%")
                                            ->orWhere('title', 'like', "%{$search}%")
                                    ->orderBy('name')
                                    ->get()
//                                    ->pluck('title', 'name')
                                    ->map(function ($row) {
                                        $row->title = $row->title ? $row->title : $row->name;
                                        return $row;
                                    })
                                ->pluck('title','id');
                            })
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $role = $this->getRecord();

        // Get all assigned abilities for this role directly from the pivot table
        $assignedAbilities = $role->abilities()
            ->select('abilities.id')
            ->get()
            ->pluck('id');

        $data['abilities'] = $assignedAbilities->toArray();

        return $data;
    }


    protected function afterSave(): void
    {
        $record = $this->getRecord();

        $abilities = collect($this->data['abilities'] ?? [])
            ->filter(function ($ability) {
                return is_numeric($ability);
            })
            ->chunk(10)
            ->map(function ($chunk) {
                return $this->getAbilitiesQuery()
                    ->whereIn('id', $chunk)
                    ->select('id')
                    ->get()
                    ->pluck('id')
                    ->toArray();
            })
            ->flatten()
            ->unique()
        ;

        /**
         * Erase what isn't in here.
         */
        $existing = Permission::where('role_id', $record->getKey())
            ->select('ability_id')
            ->get()
            ->pluck('ability_id');

        /**
         * Remove invalid entries
         */
        if (true) {
            $existing
                ->diff($abilities)
                ->chunk(10)
                ->each(function ($chunk) use ($record) {
                    Permission::where('role_id', $record->getKey())
                        ->whereIn('ability_id', $chunk)
                        ->delete();
                });
        }

        $abilities
            ->diff($existing)
            ->each(function ($ability) use ($record) {
                Permission::create([
                    'ability_id' => $ability,
                    'role_id' => $record->getKey(),
                ]);
            });
    }
}

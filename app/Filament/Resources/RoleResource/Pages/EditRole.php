<?php

namespace App\Filament\Resources\RoleResource\Pages;

use App\Filament\Resources\RoleResource;
use Filament\Actions;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;
use Silber\Bouncer\Database\Ability;
use Illuminate\Validation\Rule;
use Silber\Bouncer\Database\Role;

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
        $scope = $role->scope;

        return Rule::unique('roles', 'name')
            ->where(function ($query) use ($scope) {
                return $query->where('scope', $scope);
            })
            ->ignore($role->id);
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
                        TextInput::make('scope')
                            ->numeric()
                            ->hint('Leave empty for global roles'),
                    ])
                    ->columns(2),

                Section::make('Permissions')
                    ->description('Select the permissions for this role')
                    ->schema([
                        Select::make('abilities')
                            ->multiple()
                            ->preload()
                            ->searchable()
                            ->options(function () {
                                return \DB::table('abilities')
//                                    ->where('name', 'like', "%{$search}%")
  //                                  ->orWhere('title', 'like', "%{$search}%")
                                    ->orderBy('name')
                                    ->get()
//                                    ->pluck('title', 'name')
                                    ->map(function ($row) {
                                        $row->title = $row->title ? $row->title : $row->name;
                                        return $row;
                                    })
                                    ->pluck('title','id');
                                return $this->getAbilitiesQuery()
                                    ->orderBy('name')
                                    ->get()
                                    ->pluck('title', 'name')
                                    ->map(function ($title, $name) {
                                        return $title ?: $name;
                                    });
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


                                return $this->getAbilitiesQuery()
                                    ->where(function ($query) use ($search) {
                                        $query->where('name', 'like', "%{$search}%")
                                            ->orWhere('title', 'like', "%{$search}%");
                                    })
                                    ->orderBy('name')
                                    ->get()
                                    ->pluck('title', 'name')
                                    ->map(function ($title, $name) {
                                        return $title ?: $name;
                                    });
                            })
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $role = $this->getRecord();

        // Get all assigned abilities for this role directly from the pivot table
        $assignedAbilities = \DB::table('permissions')
            ->where('permissions.entity_type','roles')
            ->where('permissions.entity_id', $role->getKey())
            ->whereNull('permissions.scope')
            ->select('permissions.ability_id')
            ->get()
        ->pluck('ability_id');

        $data['abilities'] = $assignedAbilities->toArray();

        return $data;
    }



    protected function amutateFormDataBeforeFill(array $data): array
    {
        $role = $this->getRecord();



        // Get all abilities for this role, bypassing scope constraints
        $abilities = $role->abilities()->withoutGlobalScopes()->get();

//        dd($abilities);

        // If role is not scoped (e.g., admin), also get globally assigned abilities
        if (empty($role->scope)) {
            $globalAbilities = $role->abilities;
            $abilities = $abilities->merge($globalAbilities)->unique('id');
        }

        $data['abilities'] = $abilities->pluck('name')->toArray();

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

        \DB::table('permissions')
            ->where('entity_type', 'roles')
            ->where('entity_id', $record->getKey())
            ->whereNotNull('scope')
            ->delete();

        // Sync the abilities with the role, maintaining any scope-specific relationships
        $existing = \DB::table('permissions')
            ->where('entity_type', 'roles')
            ->where('entity_id', $record->getKey())
            ->select('ability_id')
            ->get()
            ->pluck('ability_id');

        foreach($existing->diff($abilities) as $ability) {
            \DB::table('permissions')
                ->where('entity_type', 'roles')
                ->where('entity_id', $record->getKey())
                ->where('ability_id', $ability)
                ->delete();
        }

        foreach($abilities->diff($existing) as $ability) {
            \DB::table('permissions')
                ->insert([
                    'entity_type' => 'roles',
                    'entity_id' => $record->getKey(),
                    'ability_id' => $ability,
                    'forbidden' => 0
                    ]);
        }

    }
}

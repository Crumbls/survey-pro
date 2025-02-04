<?php

namespace App\Livewire\Client;

use App\Livewire\Contracts\HasTenant;
use App\Models\Client;
use App\Models\Client as Model;
use App\Models\Tenant;
use App\Traits\HasBreadcrumbs;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;
use Filament\Tables\Actions\CreateAction;
use Silber\Bouncer\BouncerFacade;
use Silber\Bouncer\Database\Ability;
use Silber\Bouncer\Database\Role;


class ListResource extends Component implements HasForms, HasTable {
    use HasBreadcrumbs,
//        HasTenant,
        InteractsWithTable,
        InteractsWithForms;

    public ?Tenant $tenant;

    protected function getTableQuery()
    {
        $user = request()->user();

        abort_if(!$user->can('viewAny', Client::class), 403);

        /**
         * Temp patch to add in permission.
         */
        if (false) {
            if (!BouncerFacade::can('viewAny', Client::class)) {
                $ability = Ability::withoutGlobalScopes()->firstOrCreate([
                    'name' => 'viewAny',
                    'entity_id' => null,
                    'entity_type' => Client::class,
                    'scope' => $this->tenant->getKey()
                ]);
                $role = Role::find(33);
                BouncerFacade::allow($role)->to($ability);
                BouncerFacade::assign($role)->to($user);

                dd($role->abilities);
                dd($user->roles);
                dd(BouncerFacade::role()->all()->filter(function ($role) {
                    return $role->scope == $this->tenant->getKey();
                })->toArray());
//            dd($this->tenant);
                dd($user->roles, $user);
                dd($ability);
            }
            dd(__LINE__);
            \DB::enableQueryLog();

            dd(BouncerFacade::scope());
            $roles = $user->roles;
            dd($roles, \DB::getQueryLog());
            dd(request()->user()->roles);

//        dd(BouncerFacade::can('viewAny', Client::class));
            abort_if(!Gate::allows('viewAny', Model::class), 403);
        }

        if ($this->tenant) {
            $this->addBreadcrumb(__('tenants.singular').': '.$this->tenant->name, route('tenants.show', $this->tenant));
            $this->addBreadcrumb(__('clients.all'));
        } else {
            $this->addBreadcrumb(__('tenants.all'), route('surveys.index'));
        }

        $user = request()->user();

        return Model::whereRaw('1=1')
            ->when($this->tenant, function ($query) {
                $query->where('tenant_id', $this->tenant->getKey());
            })
            ->when(!$this->tenant, function ($query) use ($user) {
                $query->whereIn('tenant_id', $user->tenants()->select('tenants.id'));
            });
    }

    public function table(Table $table): Table {

    return $table
        ->query($this->getTableQuery())
        ->columns(array_filter([
            TextColumn::make('name'),
            TextColumn::make('tenant.name')
                ->label(__('tenants.singular'))
                ->sortable()
                ->toggleable(isToggledHiddenByDefault: true)
        ]))
        ->recordUrl(function (Model $record) {
            return route('clients.show', $record);
        })
        ->headerActions([
            // Add a custom button in the header
            CreateAction::make('create')
                ->label('Create New')
                ->url(function() {
                    return $this->tenant ? route('tenants.clients.create', $this->tenant) : route('clients.create');
                })
                ->button()
                ->color('custom') // Use custom color
                ->extraAttributes([
                    'class' => 'bg-primary-600 hover:bg-primary-700' // Add hover state
                ])
                ->visible(fn (): bool => true)
        ])
        ->filters([
            // ...
        ])
        ->actions([
            ActionGroup::make([
                Action::make('edit')
                    ->label(__('clients.edit'))
                    ->icon('heroicon-m-pencil-square')
                    ->url(fn ($record) => route('clients.edit', $record))
                    ->color('custom')
                    ->extraAttributes([
                        'class' => 'text-primary-600 hover:text-primary-700' // Add hover state
                    ])
                    ->disabled(fn ($record) => $record->responses_count)
                ,
                /*
                Action::make('collectors')
                    ->label('Collectors')
                    ->icon('heroicon-m-pencil-square')
                    ->url(fn ($record) => route('surveys.collectors.index', $record))
                    ->color('custom')
                    ->extraAttributes([
                        'class' => 'text-primary-600 hover:text-primary-700' // Add hover state
                    ])
//                    ->disabled(fn ($record) => !$record->getQuestionCount()),
        ,
                Action::make('reports')
                    ->label('Reports')
                    ->icon('heroicon-m-pencil-square')
                    ->url(fn ($record) => route('surveys.reports.index', $record))
                    ->color('custom')
                    ->extraAttributes([
                        'class' => 'text-primary-600 hover:text-primary-700' // Add hover state
                    ])
//                    ->disabled(fn ($record) => !$record->getQuestionCount()),
                */

            ])
                ->label('Actions')
                ->icon('heroicon-m-ellipsis-vertical')
                ->size('sm')
                ->color('custom')
                ->extraAttributes([
                    'class' => 'text-primary-600 hover:text-primary-700' // Add hover state
                ])
        ])
        ->bulkActions([
            // ...
        ]);
}

    public function render(): View {
        return view('livewire.list-resource', [
            'breadcrumbs' => $this->getBreadcrumbs(),
            'title' => __('clients.all'),
            'subtitle' => __('clients.description')
        ]);
    }
}

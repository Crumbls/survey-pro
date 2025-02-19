<?php

namespace App\Livewire\User;

use App\Models\Client;
use App\Models\Role;
use App\Models\Tenant;
use App\Models\TenantUserRole;
use App\Models\User;
use App\Traits\HasBreadcrumbs;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Columns\SelectColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;
use Filament\Tables\Actions\CreateAction;

class ListResource extends Component implements HasForms, HasTable {

    use HasBreadcrumbs,
        InteractsWithTable,
        InteractsWithForms;

    protected $isSingle = 1;

    public ?Client $client = null;
    public ?Tenant $tenant = null;

    public function mount() {
        abort_if(!Gate::allows('viewAny', \App\Models\User::class), 403);

        if ($this->client) {
            $this->tenant = $this->client->tenant;
        }

        if (!$this->tenant) {
            $user = request()->user();

            $x = $user->tenants->count();

            abort_if(!$x, 500);

            if ($user->tenants->count() == 1) {
                $this->tenant = $user->tenants->first();
            }
        }

        if ($this->client) {
            $this->addBreadcrumb(__('tenants.singular').': '.$this->tenant->name, route('tenants.show', $this->tenant));
            $this->addBreadcrumb(__('clients.singular').': '.$this->client->name, route('clients.show', $this->client));
            $this->addBreadcrumb(__('users.all'));//, route('clients.surveys.index', $this->client))   ;
        } else if ($this->tenant) {
            $this->addBreadcrumb(__('tenants.singular').': '.$this->tenant->name, route('tenants.show', $this->tenant));
            $this->addBreadcrumb(__('users.all'), route('tenants.users.index', $this->tenant));
        } else {
            $this->addBreadcrumb(__('users.all'));//, route('client.surveys.index', $this->client));
        }

    }

    protected function getTableQuery()
    {
        if ($this->client) {
            dd(__LINE__);
        } else if ($this->tenant) {
            return TenantUserRole::where('tenant_id', $this->tenant->getKey())
                ->with([
                    'role',
                    'user'
                ]);
            return $this
                ->tenant
                ->users()
                ->with('role')
                ->withPivot(['role_id'])
                ->getQuery();
        }
        return User::whereRaw('1=2');
    }

    public function table(Table $table): Table {
        $user = request()->user();

        $roles = $this->getRolesTenant();

    return $table
        ->query($this->getTableQuery())
        ->columns(array_filter([
            TextColumn::make('user.name'),
            TextColumn::make('user.email'),
            SelectColumn::make('role_id')
                ->options($this->getRolesTenant())
                ->label(__('roles.singular'))
        ]))
        ->recordUrl(function ( $record) {
            return route('users.edit', $record->user);
        })
        ->headerActions([
            // Add a custom button in the header
            CreateAction::make('create')
                ->label('Create New')
                ->url(function() {
                    $tenant = $this->tenant;
                    return $tenant ? route('tenants.users.create', $tenant) : route('users.create');
                })
                ->button()
                ->color('custom') // Use custom color
                ->extraAttributes([
                    'class' => 'bg-primary-600 hover:bg-primary-700' // Add hover state
                ])
                ->visible(function() : bool {
                    return Gate::allows('create', User::class);
                })
        ])
        ->filters([
            // ...
        ])
        ->actions([
            ActionGroup::make(array_filter([
                /*
                Action::make('edit')
                    ->label('Edit User')
                    ->icon('heroicon-m-pencil-square')
                    ->url(fn ($record) => route('users.edit', $record))
                    ->color('custom')
                    ->extraAttributes([
                        'class' => 'text-primary-600 hover:text-primary-700' // Add hover state
                    ]),
                */
                $this->tenant ? Action::make('detatch_tenant')
                    ->label(trans('users.detach_user'))
                    ->action(function(User $record) {
                        dd($record);
                    }) : null
            ]))
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
        return view('livewire.user.list-resource', [
            'breadcrumbs' => $this->getBreadcrumbs(),
            'title' => __('users.all'),
            'subtitle' => __('users.description'),
            'cancelUrl' => $this->tenant ? route('tenants.users.index', $this->tenant) : route('users.index'),
            'createText' => __('users.create')
        ]);
    }

    public function getRolesTenant() : Collection {
        return once(function() {
            return $this->tenant ? $this->tenant
                ->roles()
                ->orderBy('title','asc')
                ->get()
                ->pluck('title', 'id') :collect([]);
        });
    }

    public function updateTableRecordRole(int $recordId, string $column, $state): void
    {
        dd($recordId, $column, $state); // This should get hit when you change the select
    }
}

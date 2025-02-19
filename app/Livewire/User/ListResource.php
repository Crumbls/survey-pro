<?php

namespace App\Livewire\User;

use App\Livewire\Contracts\HasTenant;
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
use Illuminate\Database\Query\Builder;
use Illuminate\Notifications\DatabaseNotification;
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

//        dd(AssignedRo)

        if ($this->client) {

        dd(__LINE__);
        } else if ($this->tenant) {
            return $this->tenant->users()
                ->withPivot(['role_id'])
                ->getQuery();
        }
        $user = request()->user();

        /**
         * Show all users associated with any of our tenants.
         */
        return User::whereIn('users.id',
            \DB::table('tenant_user')
                ->whereIn('tenant_user.tenant_id', $user->tenants()->select('tenants.id'))
                ->select('tenant_user.user_id')
        );
    }

    public function table(Table $table): Table {
        $user = request()->user();
        $tenantCount = $user->tenants()->count();

        $roles = [];

        if ($this->tenant) {
            $roles = $this->getRolesTenant();
        }

    return $table
        ->query($this->getTableQuery())
        ->columns(array_filter([
            TextColumn::make('name'),
            TextColumn::make('email'),
//            TextColumn::make('role_id'),
            $this->tenant ? SelectColumn::make('role_id')
                ->label(__('roles.singular'))
                ->getStateUsing(function ( $record) {
//                    dd($record);
                    return $record->role_id;
                })
                ->updateStateUsing(function(int $state, $record) {
                    if ($this->tenant) {
                        dd($state, $record);
                        $existing = \DB::table('assigned_roles')
                            ->where('entity_type', User::class)
                            ->where('entity_id', $record->id)
                            ->where('scope', $this->tenant->getKey())
                            ->get();

                        if ($existing->isEmpty()) {
                            \DB::table('assigned_roles')
                                ->insert([
                                    'entity_type' => User::class,
                                    'entity_id' => $record->id,
                                    'scope' => $this->tenant->getKey(),
                                    'role_id' => $state
                                ]);
                        } else if ($existing->count() == 1) {

                            $existing = $existing->first();
                            if ($existing->role_id != $state) {
                                \DB::table('assigned_roles')
                                    ->where('id', $existing->id)
                                    ->update([
                                        'role_id' => $state
                                    ]);
                            }
                            return $state;
                        } else {
                            \DB::table('assigned_roles')
                                ->where([
                                    'entity_type' => User::class,
                                    'entity_id' => $record->id,
                                    'scope' => $this->tenant->getKey(),
                                ])
                                ->delete();

                            \DB::table('assigned_roles')
                                ->insert([
                                    'entity_type' => User::class,
                                    'entity_id' => $record->id,
                                    'scope' => $this->tenant->getKey(),
                                    'role_id' => $state
                                ]);

//                            return $state;
                        }
                    }
//                    dd($state, $record);
                })
                ->options($roles) : null

        ]))
        ->recordUrl(function (User $record) {
            return route('users.edit', $record->user_id ? $record->user_id : $record);
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
        return view('livewire.list-resource', [
            'breadcrumbs' => $this->getBreadcrumbs(),
            'title' => __('users.all'),
            'subtitle' => __('users.description'),
            'cancelUrl' => $this->tenant ? route('tenants.users.index', $this->tenant) : route('users.index'),
            'createText' => __('users.create')
        ]);
    }

    public static function getRole(int $roleId) : ?string {
        return once(function() use ($roleId) {
            return Role::find($roleId)?->title;
        });
    }

    public function getRolesTenant() : Collection {
        return once(function() {
            return $this->tenant
                ->roles()
                ->orderBy('title','asc')
                ->get()
                ->pluck('title', 'id');
        });
    }
}

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
use Filament\Notifications\Notification;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Columns\SelectColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Model;
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
            dd(__LINE__);
            $user = request()->user();

            $x = $user->tenants->count();

            abort_if(!$x, 500);

            if ($user->tenants->count() == 1) {
                $this->tenant = $user->tenants->first();
            }

        }

        if (!$this->tenant && request()->tenant) {
            dd(request()->tenant);
        }

    }

    /**
     * Get the table record key name.
     */
    public function getTableRecordKey(Model $record): string
    {
        return $record->user_id;
        dd($record);
        return 'id';
    }

    protected function getTableQuery()
    {
        if ($this->client) {
            dd(__LINE__);
        } else if ($this->tenant) {
            return $this->tenant->users()->getQuery();
            return TenantUserRole::query()
                ->where('tenant_id', $this->tenant->getKey())
                ->with([
                    'role',
                    'user'
                ]);
        }
        return User::whereRaw('1=2');
    }

    public function table(Table $table): Table {
        $user = request()->user();

        $roles = $this->getRolesTenant();

        return $table
            ->query($this->getTableQuery())
            ->columns(array_filter([
                TextColumn::make('name')
                    ->sortable(['name'])
                    ->searchable(),
                TextColumn::make('email')
                    ->sortable(['email'])
                    ->searchable(),
                SelectColumn::make('role_id')
                    ->options($this->getRolesTenant())
                    ->label(__('singular'))
                    ->disabled(function() {
                        return !$this->tenant;
                    })
                    ->updateStateUsing(function ($state, User $record) {
                        if (false && $this->center) {
                            /**
                             * TODO: Once center users are enabled.
                             */
                        } else if ($this->tenant) {
                            TenantUserRole::where([
                                'user_id' => $record->getKey(),
                                'tenant_id' => $this->tenant->getKey()
                            ])->update([
                                'role_id' => $state
                            ]);
                            // Add notification
                            Notification::make()
                                ->title(trans('user.updated'))
                                ->success()
                                ->send();

                            // This is important - refresh the table without full remount
                            $this->refreshTable();
                        }
                })
            ]))
            ->recordUrl(function ($record) {
                return route('users.edit', $record);
            })
            ->defaultSort('name')
            ->headerActions([
                CreateAction::make('create')
                    ->label('Create New')
                    ->url(function() {
                        $tenant = $this->tenant;
                        return $tenant ? route('tenants.users.create', $tenant) : route('users.create');
                    })
                    ->button()
                    ->color('custom')
                    ->extraAttributes([
                        'class' => 'bg-primary-600 hover:bg-primary-700'
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
                    $this->tenant ? Action::make('detach_tenant')
                        ->label(trans('users.detach_user'))
                        ->disabled(function(Model $record) {
                            /**
                             * TODO: Add ina  restriction to be able to lock roles.
                             **/
                            return false;
                        })
                        ->action(function($record) {

                            if (isset($this->tenant)) {
                                TenantUserRole::where('tenant_id', $this->tenant->getKey())
                                    ->where('user_id', $record->getKey())
                                    ->delete();

                                // Add notification
                                Notification::make()
                                    ->title(trans('user.detached'))
                                    ->success()
                                    ->send();

                                // This is important - refresh the table without full remount
                                $this->refreshTable();
                            } else {
                                dd(__LINE__);
                            }
                        }) : null
                ]))
                    ->label('Actions')
                    ->icon('heroicon-m-ellipsis-vertical')
                    ->size('sm')
                    ->color('custom')
                    ->extraAttributes([
                        'class' => 'text-primary-600 hover:text-primary-700'
                    ])
            ])
            ->bulkActions([
                // ...
            ]);
    }

    public function render(): View {
        if ($this->client) {
            $this->addBreadcrumb(__('tenants.singular').': '.$this->tenant->name, route('tenants.show', $this->tenant));
            $this->addBreadcrumb(__('clients.singular').': '.$this->client->name, route('clients.show', $this->client));
            $this->addBreadcrumb(__('users.all'));
        } else if ($this->tenant) {
            $this->addBreadcrumb(__('tenants.singular').': '.$this->tenant->name, route('tenants.show', $this->tenant));
            $this->addBreadcrumb(__('users.all'), route('tenants.users.index', $this->tenant));
        } else {
            dd(__LINE__);
            $this->addBreadcrumb(__('users.all'));
        }

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
                ->pluck('title', 'id') : collect([]);
        });
    }

    public function updateTableRecordRole(int $recordId, string $column, $state): void
    {
        if ($column === 'role_id' && $this->tenant) {
            $tenantUserRole = TenantUserRole::find($recordId);

            if ($tenantUserRole && $tenantUserRole->tenant_id === $this->tenant->getKey()) {
                $tenantUserRole->role_id = $state;
                $tenantUserRole->save();

                // Optional: Add notification
                $this->dispatch('notify', [
                    'type' => 'success',
                    'message' => 'User role updated successfully'
                ]);
            }
        }
    }

    // Add this method to your component
    public function refreshTable()
    {
        $this->dispatch('refreshTable');
    }
}

<?php

namespace App\Livewire\User;

use App\Livewire\Contracts\HasTenant;
use App\Models\TenantUserRole;
use App\Models\User as Model;
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
use Illuminate\Database\Query\Builder;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;
use Filament\Tables\Actions\CreateAction;


class ListResource extends Component implements HasForms, HasTable {
    use HasBreadcrumbs,
        HasTenant,
        InteractsWithTable,
        InteractsWithForms;

    protected $isSingle = 1;
    public function mount() {
        $tenantId = request()->tenantId;

        $this->setTenant($tenantId);

        $tenant = $this->getTenant();

        $user = request()->user();

        if (!$tenant) {
            if ($user->tenants()->count() == 1) {
                $tenant = $user->tenants()->first();
                return redirect()->route('tenants.users.index', $tenant);
            }
        }
    }

    protected function getTableQuery()
    {
        abort_if(!Gate::allows('viewAny', Model::class), 403);

        $tenant = $this->getTenant();
        $user = request()->user();

        if ($tenant) {
            $this->addBreadcrumb('Center: '.$tenant->name, route('tenants.show', $tenant));
            $this->addBreadcrumb('All Users');
            $this->isSingle = true;
        } else {
            $this->addBreadcrumb('All Users');
            $this->isSingle = false;
        }

        $tenant = $this->getTenant();

        $query = Model::whereRaw('1=1')
            ->when($tenant, function ($query, $tenant) {
                return $query->whereExists(function ($subquery) use ($tenant) {
                    $subquery->from('tenant_user_role')
                        ->whereColumn('tenant_user_role.user_id', 'users.id')
                        ->where('tenant_user_role.tenant_id', $tenant->id);
                })
                    ->with(['tenants' => function ($query) use ($tenant) {
                        $query->where('tenants.id', $tenant->id);
                    }]);
            })
            ->when(!$tenant, function ($query) use ($user) {
                /**
                 * Problem here where it isn't showing all users associated with the tenants this user belongs to.
                 * Move it to a tenant_id query.
                 */
                $query->whereIn('users.id', \DB::table('tenant_user_role')
                    ->whereIn('tenant_user_role.tenant_id', $user->tenants->pluck('id'))
                    ->select('tenant_user_role.user_id')
                );
//                dd($user->tenants);
                return;
// select "tenant_user_role"."user_id" from "tenants" inner join "tenant_user_role" on "tenants"."id" = "tenant_user_role"."tenant_id" where "tenant_user_role"."user_id" = 4 and "tenants"."deleted_at" is null
//                dd($user->tenants()->select('tenant_user_role.user_id')->get());
                $query->whereIn('users.id', $user->tenants()->select('tenant_user_role.user_id'));
            });

        // Join with the role table to get role data in a single query
        if ($tenant) {
            $query->leftJoin('tenant_user_role', function ($join) use ($tenant) {
                $join->on('users.id', '=', 'tenant_user_role.user_id')
                    ->where('tenant_user_role.tenant_id', '=', $tenant->id);
            })
                ->leftJoin('roles', 'tenant_user_role.role_id', '=', 'roles.id')
                ->select('users.*', 'roles.name as role_name');
        }
        /*
\DB::enableQueryLog();
        dd($query->get(), \DB::getQueryLog());
        */
        return $query;

        // below works, kind of.

        return Model::whereRaw('1=1')
            ->when($tenant, function ($query, $tenant) {
                $query->whereIn('users.id', $tenant->users()->select('tenant_user_role.user_id'));
            })
            ->when(!$tenant, function ($query) use ($user) {
                $query->whereIn('users.id', $user->tenants()->select('tenant_user_role.user_id'));
            });

    }

    public function table(Table $table): Table {
        $user = request()->user();
        $tenantCount = $user->tenants()->count();
    return $table
        ->query($this->getTableQuery())
        ->columns(array_filter([
            TextColumn::make('name'),
            TextColumn::make('email'),
            $this->isSingle ? TextColumn::make('role_name')
                ->label('Role') : null

        ]))
        ->recordUrl(function (Model $record) {
            return '#';

            return route('users.edit', $record);
        })
        ->headerActions([
            // Add a custom button in the header
            CreateAction::make('create')
                ->label('Create New')
                ->url(function() {
                    $tenant = $this->getTenant();
                    return $tenant ? route('tenants.users.create', $tenant) : route('users.create');
                })
                ->button()
                ->color('custom') // Use custom color
                ->extraAttributes([
                    'class' => 'bg-primary-600 hover:bg-primary-700' // Add hover state
                ])
                ->visible(function() : bool {
                    return Gate::allows('create', Model::class);
                })
        ])
        ->filters([
            // ...
        ])
        ->actions([
            ActionGroup::make([
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
                Action::make('collectors')
                    ->label('Collectors')
                    ->icon('heroicon-m-pencil-square')
                    ->url(fn ($record) => route('surveys.collectors.index', $record))
                    ->color('custom')
                    ->extraAttributes([
                        'class' => 'text-primary-600 hover:text-primary-700' // Add hover state
                    ]),
                Action::make('reports')
                    ->label('Reports')
                    ->icon('heroicon-m-pencil-square')
                    ->url(fn ($record) => route('survey.reports.index', $record))
                    ->color('custom')
                    ->extraAttributes([
                        'class' => 'text-primary-600 hover:text-primary-700' // Add hover state
                    ]),

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
            'breadcrumbs' => $this->getBreadcrumbs()
        ]);
    }
}

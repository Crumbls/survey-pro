<?php

namespace App\Livewire\User;

use App\Livewire\Contracts\HasTenant;
use App\Models\Client;
use App\Models\Tenant;
use App\Models\TenantUserRole;
use App\Models\User;
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
        if ($this->client) {

        } else if ($this->tenant) {
            return $this->tenant->users()->getQuery();
        }

        return User::query();


    }

    public function table(Table $table): Table {
        $user = request()->user();
        $tenantCount = $user->tenants()->count();
    return $table
        ->query($this->getTableQuery())
        ->columns(array_filter([
            TextColumn::make('name'),
            TextColumn::make('email')

        ]))
        ->recordUrl(function (Model $record) {
            return route('users.edit', $record);
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
                    ->url(fn ($record) => route('surveys.reports.index', $record))
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
            'breadcrumbs' => $this->getBreadcrumbs(),
            'title' => __('users.create'),
            'subtitle' => __('users.description'),
            'cancelUrl' => $this->tenant ? route('tenants.users.index', $this->tenant) : route('users.index'),
            'createText' => __('users.create')
        ]);
    }
}

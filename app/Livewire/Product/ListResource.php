<?php

namespace App\Livewire\Product;

use App\Livewire\Contracts\HasTenant;
use App\Models\Client;
use App\Models\Client as Model;
use App\Models\Product;
use App\Models\Tenant;
use App\Models\User;
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

class ListResource extends Component implements HasForms, HasTable {
    use HasBreadcrumbs,
//        HasTenant,
        InteractsWithTable,
        InteractsWithForms;

    public ?Tenant $tenant;

    public function mount() {

        $user = request()->user();

        if (!$user->can('viewAny', Product::class) && !isset($this->tenant)) {
            /**
             * If they have a single one, transfer them over.
             */
            $tenantId =
                \DB::table('assigned_roles')
                    ->whereIn('assigned_roles.role_id',
                        \DB::table('permissions')
                            ->whereIn('ability_id',
                                \DB::table('abilities')
                                    ->where('name', 'viewAny')
                                    ->where('entity_type', Client::class)
                                    ->whereNull('scope')
                                    ->select('id')
                            )
                            ->where('entity_type', 'roles')
                            ->select('entity_id')
                    )
                    ->where('entity_type', User::class)
                    ->where('entity_id', $user->id)
                    ->select('scope')
                    ->get()
                    ->pluck('scope')
                    ->unique();

            abort_if($tenantId->count() !== 1, 403);

            $this->tenant = Tenant::find($tenantId->first());

            return redirect()->route('tenants.clients.index', $this->tenant);
        } else {
            abort_if(!$user->can('viewAny', Client::class), 403);

        }

        if (isset($this->tenant) && $this->tenant) {
            $this->addBreadcrumb(__('tenants.singular').': '.$this->tenant->name, route('tenants.show', $this->tenant));
            $this->addBreadcrumb(__('products.all'));
        } else {
            $this->addBreadcrumb(__('tenants.all'), route('tenants.index'));
        }
    }
    protected function getTableQuery()
    {
        if (isset($this->tenant)) {
            return $this->tenant->products()->getQuery();
        }

        $user = request()->user();

        return Product::whereIn('tenant_id', $user->tenants()->select('tenants.id'));
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
        ->recordUrl(function (Product $record) {
            if (Gate::allows('update', $record)) {
                return route('products.edit', $record);
            }
            if (Gate::allows('view', $record)) {
                return route('products.show', $record);
            }
            return null;
        })
        ->headerActions([
            // Add a custom button in the header
            CreateAction::make('create')
                ->label('Create New')
                ->url(function() {
                    return isset($this->tenant) && $this->tenant ? route('tenants.products.create', $this->tenant) : route('products.create');
                })
                ->button()
                ->color('custom') // Use custom color
                ->extraAttributes([
                    'class' => 'bg-primary-600 hover:bg-primary-700' // Add hover state
                ])
                ->visible(function() {
                    return Gate::allows('create', Client::class);
                })
        ])
        ->filters([
            // ...
        ])
        ->actions([
            ActionGroup::make([
                Action::make('edit')
                    ->label(__('products.edit'))
                    ->icon('heroicon-m-pencil-square')
                    ->url(function($record) {
                        if (Gate::allows('update', $record)) {
                            return route('products.edit', $record);
                        }
                        if (Gate::allows('view', $record)) {
                            return route('products.show', $record);
                        }
                        return null;
                    })
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
            'title' => __('products.all'),
            'subtitle' => __('products.description')
        ]);
    }
}

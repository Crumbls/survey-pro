<?php

namespace App\Livewire\Report;

use App\Livewire\Contracts\HasTenant;
use App\Models\Collector;
use App\Models\Report as Model;
use App\Models\Survey;
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
use Illuminate\Database\Query\Builder;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Livewire\Component;
use Filament\Tables\Actions\CreateAction;


class ListResource extends Component implements HasForms, HasTable {
    use HasBreadcrumbs,
        HasTenant,
        InteractsWithTable,
        InteractsWithForms;

    public $isSingle = false;


    public function mount() {
        $tenantId = request()->tenantId;

        $this->setTenant($tenantId);

        $tenant = $this->getTenant();

        $user = request()->user();

        if (!$tenant) {
            if ($user->tenants()->count() == 1) {
                $tenant = $user->tenants()->first();
                if (!Gate::allows('viewAny', \App\Models\Report::class)) {
                    return redirect()->route('tenants.reports.show', $tenant);
                }
            }
        }

        if ($tenant) {
            $this->addBreadcrumb('Center: '.$tenant->name, route('tenants.show', $tenant));
        } else {
            $this->addBreadcrumb('All Centers', route('surveys.index'));
        }
    }
    protected function getTableQuery()
    {
        $tenant = $this->getTenant();

        $user = request()->user();

        return Model::query()
            ->when($tenant, function ($query) use ($tenant) {
                $this->isSingle = true;
                $query->where('tenant_id', $tenant->getKey());
            })
            ->when(!$tenant, function ($query) use ($user) {
                if (!Gate::allows('viewAny', Model::class)) {
                    $this->isSingle = true;

                    $query->whereIn('reports.id', $user->tenants()->select('tenants.id'));
                } else {
                    $this->isSingle = false;

                }

            })
            ;



    }

    public function table(Table $table): Table {
        $user = request()->user();
        $tenantCount = $user->tenants->count();
//        dd($this->getTenant());
//        dd($this->isSingle);
//        $isSingle = ;
    return $table
        ->query($this->getTableQuery())
        ->columns(array_filter([
            TextColumn::make('title'),
//            TextColumn::make('type'),
  //          TextColumn::make('status'),
            $tenantCount ? TextColumn::make('survey.tenant.name')->label('Center') : null

        ]))
        ->recordUrl(function (Model $record) {
            return route('reports.show', $record);
        })
        ->headerActions([
            // Add a custom button in the header
            CreateAction::make('create')
                ->label('Create New')
                ->url(function() : string {
                    $tenant = $this->getTenant();

                    return $tenant ? route('tenants.reports.create', $tenant) : route('reports.create');
                    dd($tenant);

                    return route('surveys.reports.create');
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
                Action::make('view')
                    ->label('View')
                    ->icon('heroicon-m-pencil-square')
                    ->url(function (Model $record) {
                        return route('reports.show', $record);
                    })
                    ->color('custom')
                    ->extraAttributes([
                        'class' => 'text-primary-600 hover:text-primary-700' // Add hover state
                    ]),
                Action::make('edit')
                    ->label('Edit')
                    ->icon('heroicon-m-pencil-square')
                    ->url(fn ($record) => route('reports.edit', $record))
                    ->color('custom')
                    ->extraAttributes([
                        'class' => 'text-primary-600 hover:text-primary-700' // Add hover state
                    ])
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
        return view('livewire.report.list-resource', [
            'breadcrumbs' => $this->getBreadcrumbs(),
        ]);
    }
}

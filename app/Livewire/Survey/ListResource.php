<?php

namespace App\Livewire\Survey;

use App\Livewire\Contracts\HasTenant;
use App\Models\Survey as Model;
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



    protected function getTableQuery()
    {
        abort_if(!Gate::allows('viewAny', Model::class), 403);

        $tenantId = request()->tenantId;

        $this->setTenant($tenantId);

        $tenant = $this->getTenant();

        $user = request()->user();

        if (!$tenant) {
            if ($user->tenants()->count() == 1) {
                $tenant = $user->tenants()->first();
                $this->setTenant($tenant);
//                return redirect()->route('tenants.surveys.index', $tenant);
            }
        }

        if ($tenant) {
            $this->addBreadcrumb('Center: '.$tenant->name, route('tenants.show', $tenant));
            $this->addBreadcrumb('Surveys', route('tenants.surveys.index', $tenant));
        } else {
            $this->addBreadcrumb('All Surveys', route('surveys.index'));
        }

        return Model::whereRaw('1=1')
            ->when($tenant, function ($query, $tenant) {
                $query->where('tenant_id', $tenant->getKey());
            })
            ->when(!$tenant, function ($query) use ($user) {
                $query->whereIn('tenant_id', $user->tenants()->select('tenants.id'));
            });
    }

    public function table(Table $table): Table {
        $user = request()->user();
        $tenantCount = $user->tenants()->count();

    return $table
        ->query($this->getTableQuery())
        ->columns(array_filter([
            TextColumn::make('title'),
            $tenantCount ? TextColumn::make('tenant.name')->label('Center') : null
        ]))
        ->recordUrl(function (Model $record) {
            return route('surveys.show', $record);
        })
        ->headerActions([
            // Add a custom button in the header
            CreateAction::make('create')
                ->label('Create New')
                ->url(function() {
                    $tenant = $this->getTenant();
                    return $tenant ? route('tenants.surveys.create', $tenant) : route('surveys.create');
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
                    ->label('Edit Survey')
                    ->icon('heroicon-m-pencil-square')
                    ->url(fn ($record) => route('surveys.edit', $record))
                    ->color('custom')
                    ->extraAttributes([
                        'class' => 'text-primary-600 hover:text-primary-700' // Add hover state
                    ]),
                Action::make('collectors')
                    ->label('Collectors')
                    ->icon('heroicon-m-pencil-square')
                    ->url(fn ($record) => route('survey.collectors.index', $record))
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
        return view('livewire.survey.list-resource', [
            'breadcrumbs' => $this->getBreadcrumbs()
        ]);
    }
}

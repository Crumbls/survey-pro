<?php

namespace App\Livewire\Survey;

use App\Livewire\Contracts\HasTenant;
use App\Models\Client;
use App\Models\Survey;
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
//        HasTenant,
        InteractsWithTable,
        InteractsWithForms;

    public ?Client $client = null;
    public ?Tenant $tenant = null;

    public function mount() {
        abort_if(!Gate::allows('viewAny', Model::class), 403);

        if ($this->client) {
            $this->tenant = $this->client->tenant;
            $this->addBreadcrumb(trans('tenants.singular').': '.$this->tenant->name, route('tenants.show', $this->tenant));
            $this->addBreadcrumb(trans('clients.singular').': '.$this->client->name, route('clients.show', $this->client));
            $this->addBreadcrumb(trans('surveys.all'));//, route('clients.surveys.index', $this->client))   ;
        } else if ($this->tenant) {
            $this->addBreadcrumb(trans('tenants.singular').': '.$this->tenant->name, route('tenants.show', $this->tenant));
            $this->addBreadcrumb(trans('surveys.all'), route('tenants.surveys.index', $this->tenant));
        } else {
            $this->addBreadcrumb(trans('surveys.all'));//, route('client.surveys.index', $this->client));
        }
    }

    protected function getTableQuery()
    {

        if ($this->client) {
            return $this->client->surveys()
                ->with(['client', 'client.tenant'])
                ->withCount('responses')
                ->getQuery();

        } else if ($this->tenant) {
            return Survey::whereIn('client_id', $this->tenant->clients()->select('clients.id'))
                ->with(['client', 'client.tenant'])
                ->withCount('responses');

        }

        $user = request()->user();

        return Survey::whereIn('client_id',
            Client::whereIn('tenant_id', $user->tenants()->select('tenants.id'))->select('clients.id')
        )
            ->with(['client', 'client.tenant'])
            ->withCount('responses')
            ;
    }

    public function table(Table $table): Table {
        $user = request()->user();
        $tenantCount = $user->tenants()->count();

    return $table
        ->query($this->getTableQuery())
        ->columns(array_filter([
            TextColumn::make('title'),
            TextColumn::make('responses')
                ->getStateUsing(function (Model $record) {
                    return number_format($record->responses_count);
                }),
            TextColumn::make('questions')
                ->getStateUsing(function (Model $record) {

                    return number_format($record->getQuestionCount());
                }),
            $tenantCount ? TextColumn::make('client.tenant.name')
                ->label(trans('tenants.singular'))
                ->sortable()
                ->toggleable(isToggledHiddenByDefault: true)
                : null,
            TextColumn::make('client.name')
                ->label(trans('clients.singular'))
                ->sortable()
                ->toggleable(isToggledHiddenByDefault: true),
        ]))
        ->recordUrl(function (Model $record) {
            return route('surveys.show', $record);
        })
        ->headerActions([
            // Add a custom button in the header
            CreateAction::make('create')
                ->label('Create New')
                ->url(function() {
                    if ($this->client) {
                        return route('clients.surveys.create', $this->client);
                    } else if ($this->tenant) {
                        return route('tenants.surveys.create', $this->tenant);
                    }
                    return route('surveys.create');
                })
                ->button()
                ->color('custom') // Use custom color
                ->extraAttributes([
                    'class' => 'bg-primary-600 hover:bg-primary-700' // Add hover state
                ])
                ->visible(function() {
                    return Gate::allows('create', Survey::class);
                })
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
                    ])
                    ->disabled(fn ($record) => $record->responses_count)
                ,
                Action::make('collectors')
                    ->label('Collectors')
                    ->icon('heroicon-m-pencil-square')
                    ->url(fn ($record) => route('surveys.collectors.index', $record))
                    ->color('custom')
                    ->extraAttributes([
                        'class' => 'text-primary-600 hover:text-primary-700' // Add hover state
                    ])
                    ->disabled(fn ($record) => !$record->getQuestionCount()),
                Action::make('reports')
                    ->label('Reports')
                    ->icon('heroicon-m-pencil-square')
                    ->url(fn ($record) => route('surveys.reports.index', $record))
                    ->color('custom')
                    ->extraAttributes([
                        'class' => 'text-primary-600 hover:text-primary-700' // Add hover state
                    ])
                    ->disabled(fn ($record) => !$record->getQuestionCount()),
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

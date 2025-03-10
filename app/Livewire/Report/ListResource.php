<?php

namespace App\Livewire\Report;

use App\Filament\Actions\DownloadReportAction;
use App\Livewire\Contracts\HasTenant;
use App\Models\Client;
use App\Models\Collector;

use App\Models\Report;
use App\Models\Report as Model;
use App\Models\Response;
use App\Models\Survey;
use App\Models\Tenant;
use App\Models\User;
use App\Traits\HasBreadcrumbs;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Query\Builder;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;
use Livewire\Component;
use Filament\Tables\Actions\CreateAction;
use Silber\Bouncer\BouncerFacade;


class ListResource extends Component implements HasForms, HasTable {
    use HasBreadcrumbs,
        InteractsWithTable,
        InteractsWithForms;

    public $isSingle = false;

    public ?Collector $collector = null;
    public ?Survey $survey = null;
    public ?Client $client = null;
    public ?Tenant $tenant = null;
//    public $surveyId;

    public function mount() {

        if ($this->survey) {
            $this->client = $this->survey->client;
            $this->tenant = $this->client->tenant;
            $this->addBreadcrumb(__('tenants.singular').': '.$this->tenant->name, route('tenants.show', $this->tenant));
            $this->addBreadcrumb(__('clients.singular').': '.$this->client->name, route('clients.show', $this->client));
            $this->addBreadcrumb(__('reports.all'));
        } else if ($this->client) {
            $this->tenant = $this->client->tenant;
            $this->addBreadcrumb(__('tenants.singular').': '.$this->tenant->name, route('tenants.show', $this->tenant));
            $this->addBreadcrumb(__('clients.singular').': '.$this->client->name, route('clients.show', $this->client));
            $this->addBreadcrumb(__('reports.all'));
        } elseif ($this->tenant) {
            $this->addBreadcrumb(__('tenants.singular').': '.$this->tenant->name, route('tenants.show', $this->tenant));
            $this->addBreadcrumb(__('reports.all'));
        } else {
            $this->addBreadcrumb(__('reports.all'));//, route('surveys.index'));
        }

    }
    protected function getTableQuery()
    {
        if ($this->collector) {
            abort(500);
        } else if ($this->survey) {
            return $this->survey->reports()->getQuery();
        } else if ($this->client) {
            return $this->client->reports()
                ->getQuery();
        } else if ($this->tenant) {
            return Report::whereIn('reports.client_id', $this->tenant->clients()->select('clients.id'))
                ->with(['survey','client']);
        } else {
            $user = request()->user();
            return Report::whereIn('reports.survey_id', Survey::whereIn('surveys.tenant_id', $user->tenants()->select('tenants.id'))->select('surveys.id'))
                ->with([
                    'client',
                    'client.tenant'
                ]);
        }
    }

    public function table(Table $table): Table {
        $user = request()->user();
        $tenantCount = $user->tenants->count();
    return $table
        ->query($this->getTableQuery())
        ->columns(array_filter([
            $tenantCount ? TextColumn::make('survey.client.tenant.name')
                ->label(__('tenants.singular'))
                ->sortable()
                ->toggleable(isToggledHiddenByDefault: true)
                : null,
            TextColumn::make('survey.client.name')
                ->label(trans('clients.singular'))
                ->sortable()
                ->toggleable(isToggledHiddenByDefault: true),
            TextColumn::make('title'),
            TextColumn::make('created_at')
                ->dateTime()
                ->sortable()
                ->toggleable(isToggledHiddenByDefault: true),
            TextColumn::make('survey.title')
                ->sortable(),

        ]))
        ->recordUrl(function (Model $record) {
            return route('reports.edit', $record);
        })
        ->headerActions([
            // Add a custom button in the header
            CreateAction::make('create')
                ->label('Create New')
                ->url(function() : string {
                    if ($this->collector) {
                        return route('collectors.reports.create', $this->collector);
                    } else if ($this->survey) {
                        return route('surveys.reports.create', $this->survey);
                    } else if ($this->client) {
                        return route('clients.reports.create', $this->client);
                    } else if ($this->tenant) {
                        return route('tenants.reports.create', $this->tenant);
                    }

                    return route('reports.create');
                })
                ->button()
                ->color('custom') // Use custom color
                ->extraAttributes([
                    'class' => 'bg-primary-600 hover:bg-primary-700' // Add hover state
                ])
                ->visible(function() {
                    if (Gate::denies('create', Report::class)) {
                        return false;
                    }
                    /**
                     * Find surveys that have responses.,
                     */
                    if ($this->client) {
                        return Response::whereIn('survey_id',
                            Survey::select('id')
                                ->where('surveys.client_id', $this->client->getKey())
                            )
                            ->take(1)
                            ->exists();
                    }
                    if ($this->tenant) {
                        return Response::whereIn('survey_id',
                            Survey::select('id')
                                ->whereIn('surveys.client_id', Client::select('clients.id')
                                ->where('tenant_id', $this->tenant->getKey()))
                            )
                            ->take(1)
                            ->exists();
                    }
                    return Response::take(1)
                        ->exists();
                })
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
                    ->openUrlInNewTab()
                    ->color('custom')
                    ->extraAttributes([
                        'class' => 'text-primary-600 hover:text-primary-700' // Add hover state
                    ]),
                /*
                DownloadReportAction::make('download')
                    ->extraAttributes([
//                        'data-record-id' => fn (Model $record): string => $record->id,
                    ]),
                */
                Action::make('edit')
                    ->label('Edit')
                    ->icon('heroicon-m-pencil-square')
                    ->url(fn ($record) => route('reports.edit', $record))
                    ->color('custom')
                    ->hidden(function (Model $record) {
                        return !Gate::allows('update', $record);
                    })
                    ->extraAttributes([
                        'class' => 'text-primary-600 hover:text-primary-700' // Add hover state
                    ]),
                DeleteAction::make('delete')
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
            'title' => __('clients.create'),
            'subtitle' => __('clients.description'),
            'cancelUrl' => $this->tenant ? route('tenants.clients.index', $this->tenant) : route('clients.index'),
            'createText' => __('clients.create')
        ]);
    }
}

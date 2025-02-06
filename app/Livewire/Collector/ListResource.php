<?php

namespace App\Livewire\Collector;

use App\Models\Client;
use App\Models\Collector;
use App\Models\Collector as Model;
use App\Models\Report;
use App\Models\Survey;
use App\Models\Tenant;
use App\Models\User;
use App\Traits\HasBreadcrumbs;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Query\Builder;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Str;
use Livewire\Component;
use Filament\Tables\Actions\CreateAction;


class ListResource extends Component implements HasForms, HasTable {
    use HasBreadcrumbs,
        InteractsWithTable,
        InteractsWithForms;

    public ?Client $client = null;
    public ?Tenant $tenant = null;
    public ?Survey $survey = null;

    public function mount() {
        if ($this->survey) {
            $this->client = $this->survey->client;
            $this->tenant = $this->client->tenant;
            $this->addBreadcrumb(__('tenants.singular').': '.$this->tenant->name, route('tenants.show', $this->tenant));
            $this->addBreadcrumb(__('clients.singular').': '.$this->client->name, route('clients.show', $this->client));
            $this->addBreadcrumb(__('surveys.singular').': '.$this->survey->title, route('surveys.show', $this->survey));
            $this->addBreadcrumb(__('collectors.all'));
        } else if ($this->client) {
            $this->tenant = $this->client->tenant;
            $this->addBreadcrumb(__('tenants.singular').': '.$this->tenant->name, route('tenants.show', $this->tenant));
            $this->addBreadcrumb(__('clients.singular').': '.$this->client->name, route('clients.show', $this->client));
            $this->addBreadcrumb(__('collectors.all'));
        } elseif ($this->tenant) {
            $this->addBreadcrumb(__('tenants.singular').': '.$this->tenant->name, route('tenants.show', $this->tenant));
            $this->addBreadcrumb(__('collectors.all'));
        } else {
            $this->addBreadcrumb(__('collectors.all'));//, route('surveys.index'));
        }

    }

    protected function getTableQuery()
    {
        if ($this->survey) {
            return $this->survey->collectors()->getQuery();
        } else if ($this->client) {
            return Collector::where('client_id', $this->client->getKey())
                ->with([
                    'client',
                    'client.tenant'
                ]);
        } elseif ($this->tenant) {
            return Collector::whereIn('survey_id', Survey::where('tenant_id', $this->tenant->getKey())->select('surveys.id'))
                ->with([
                    'client',
                    'client.tenant'
                ]);
        } else {
            return Collector::whereIn('client_id', Client::whereIn('tenant_id', request()->user()->tenants()->select('tenants.id'))->select('clients.id'))
                ->with([
                    'client',
                    'client.tenant'
                ]);
        }
        if (isset($this->survey)) {
            return $this->survey->collectors()->withCount('responses')->getQuery();
        } else if ($this->surveyId) {
        } else if ($this->tenantId) {
            $tenant = $this->tenant;
//            dd(Collector::all()->random()->toArray());
            return Collector::whereRaw('1=1')
                ->whereIn('survey_id',
                    $tenant
                        ->surveys()
                        ->select('surveys.id')
                )
                ->withCount('responses');
        } else {
            dd(__LINE__);

        }

        $user = request()->user();

        if ($user->tenants->count() == 1) {
            $tenant = $user->tenants->first();

            $this->addBreadcrumb(trans('tenants.singular').': '.$tenant->name, route('tenants.show', $tenant));

            if ($tenant->surveys->count() == 1) {
                $survey = $tenant->surveys->first();
                $this->addBreadcrumb('Survey: '.$survey->title, route('surveys.show', $survey));
            } else {
//                $this->addBreadcrumb('All Surveys', route('tenants.surveys.index', $tenant));
            }

            $this->addBreadcrumb('All Collectors');

        } else {
            $this->addBreadcrumb(trans('tenants.all'), route('centers.index'));
            $this->addBreadcrumb('All Surveys', route('surveys.index'));
            $this->addBreadcrumb('All Collectors');
        }

        return Model::whereIn('survey_id', Survey::whereIn('tenant_id', $user->tenants()->select('tenants.id'))->select('surveys.id'));
    }

    public function table(Table $table): Table {
        $user = request()->user();
        $tenantCount = $user->tenants()->count();
    return $table
        ->query($this->getTableQuery())
        ->columns([
            TextColumn::make('name'),
            TextColumn::make('type'),
            TextColumn::make('status'),
            TextColumn::make('responses_count')
                ->counts('responses')
                ->label('Responses'),

            $tenantCount ? TextColumn::make('client.tenant.name')
                ->label(trans('tenants.singular'))
                ->sortable()
                ->toggleable(isToggledHiddenByDefault: true)
                : null,

            TextColumn::make('client.name')
                ->label(trans('clients.singular'))
                ->sortable()
                ->toggleable(isToggledHiddenByDefault: true),

        ])
        ->recordUrl(function (Model $record) {
            return $record->status == 'open' && $record->type == 'url' ? url('/r/'.$record->unique_code) : '';
        })
        ->headerActions([
            // Add a custom button in the header
            CreateAction::make('create')
                ->label('Create New')
                ->url(function() : string {

                    if ($this->survey) {
                        return route('surveys.collectors.create', $this->survey);
                    } else if ($this->client) {
                        return route('clients.collectors.create', $this->client);
                    } else if ($this->tenant) {
                        return route('tenants.collectors.create', $this->tenant);
                    }

                    return route('collectors.create');
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
                    ->url(function ($record) {
                        return $record->status == 'open' && $record->type == 'url' ? route('collector.show', $record->unique_code) : '';
                    })
                    ->color('custom')
                    ->extraAttributes([
                        'class' => 'text-primary-600 hover:text-primary-700' // Add hover state
                    ]),
                Action::make('open')
                    ->label('Open')
                    ->icon('heroicon-m-pencil-square')
                    ->color('custom')
                    ->extraAttributes([
                        'class' => 'text-primary-600 hover:text-primary-700' // Add hover state
                    ])
                    ->hidden(function (Model $record) {
                        return $record->status == 'open';
                    })
                    ->action(function(Model $record) {
                        $record->update([
                            'status' => 'open'
                        ]);
                    }),
                Action::make('close')
                    ->label('Close')
                    ->icon('heroicon-m-pencil-square')
                    ->color('custom')
                    ->extraAttributes([
                        'class' => 'text-secondary-600 hover:text-secondary-700' // Add hover state
                    ])
                    ->action(function(Model $record) {
                        $record->update([
                            'status' => 'closed'
                        ]);
                    })
                    ->hidden(function (Model $record) {
                        return $record->status == 'closed';
                    }),
                Action::make('report-create')
                    ->label('Create Report')
                    ->icon('heroicon-m-pencil-square')
                    ->color('custom')
                    ->extraAttributes([
                        'class' => 'text-secondary-600 hover:text-secondary-700' // Add hover state
                    ])
                    ->action(function(Model $record) {
                        $report = new Report([
                            'client_id' => $record->client->getKey(),
                            'title' => 'Report for '.$record->name,
                            'collector_ids' => [$record->getKey()],
                            /**
                             * @deprecated
                             */
                            'survey_id' => $record->survey->getKey(),
                        ]);
                        $report->save();
                        Notification::make(trans('reports.created'));
                        return redirect()->route('reports.edit', $report);
                    })
                    ->hidden(function (Model $record) {
                        return !$record->responses()->count();
                    })

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
        return view('livewire.collector.list-resource', [
            'breadcrumbs' => $this->getBreadcrumbs()
        ]);
    }
}

<?php

namespace App\Livewire\Response;

use App\Livewire\Contracts\HasTenant;
use App\Models\Client;
use App\Models\Collector;
use App\Models\Response;
use App\Models\Survey;
use App\Models\Tenant;
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

class ListResource extends Component implements HasForms, HasTable
{
    use HasBreadcrumbs,
        InteractsWithTable,
        InteractsWithForms;

    protected $isSingle = 1;

    public ?Collector $collector = null;
    public ?Survey $survey = null;
    public ?Client $client = null;
    public ?Tenant $tenant = null;

    public function mount()
    {
        abort_if(!Gate::allows('viewAny', Response::class), 403);

        if (isset($this->collector) && $this->collector) {
            $this->survey = $this->collector->survey;
        }
        if ($this->survey) {
            $this->client = $this->survey->client;
        }
        if ($this->client) {
            $this->tenant = $this->client->tenant;
        }

        abort_if(!$this->tenant, 403);

        /**
         * Verify they exist as part of the tenant.
         * TODO: Just use middleware.
         */
        abort_if(!request()->user()->tenants()->where('tenants.id', $this->tenant->getKey())->exists(), 403);


    }

    protected function getTableQuery()
    {

//        $tenant = $this->getTenant();
        $user = request()->user();

        if (isset($this->collector)) {
            return $this->collector->responses()->getQuery();
        } else if (isset($this->survey)) {
            return $this->survey->responses()->getQuery();
        } else if ($this->client) {
            return Response::whereIn(
                'collector_id',
                Collector::whereIn(
                    'survey_id',
                    Survey::where('surveys.client_id', $this->client->getKey())->select('surveys.id')
                )->select('collectors.id')
            );
        }

        return Collector::query();
    }

    public function table(Table $table): Table
    {
        return $table
            ->query($this->getTableQuery())
            ->columns([
//                TextColumn::make('id'),

                TextColumn::make('survey.client.tenant.name')
                    ->label(trans('tenants.singular'))
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                ,
                TextColumn::make('survey.client.name')
                    ->label(trans('clients.singular'))
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                ,
            TextColumn::make('survey.title')
                ->label('Survey'),
            TextColumn::make('collector.name')
                ->label('Collector'),
            TextColumn::make('data'),
            TextColumn::make('created_at')
                ->label('Created At')
                ->dateTime()
                ->sortable()
                ->toggleable(isToggledHiddenByDefault: true),
            /*
            TextColumn::make('updated_at')
                ->label('Updated At')
                ->dateTime()
            */
            ])
            ->headerActions([
                /*
                CreateAction::make('create')
                    ->label('Create New')
                    ->url(function() {
                        $tenant = $this->getTenant();
                        return $tenant ? route('tenants.Response.create', $tenant) : route('Response.create');
                    })
                    ->button()
                    ->color('custom')
                    ->extraAttributes([
                        'class' => 'bg-primary-600 hover:bg-primary-700'
                    ])
                    ->visible(fn() => Gate::allows('create', Model::class))
                */
            ])
            ->actions([
                ActionGroup::make([
                    Action::make('edit')
                        ->label('Edit')
                        ->icon('heroicon-m-pencil-square')
                        ->url(fn ($record) => '#')
                        ->color('custom')
                        ->extraAttributes([
                            'class' => 'text-primary-600 hover:text-primary-700'
                        ])
                ])
                ->label('Actions')
                ->icon('heroicon-m-ellipsis-vertical')
                ->size('sm')
                ->color('custom')
                ->extraAttributes([
                    'class' => 'text-primary-600 hover:text-primary-700'
                ])
            ]);
    }

    public function render(): View
    {
        if ($this->collector) {
            $this->tenant = $this->client->tenant;
            $this->addBreadcrumb(__('tenants.singular').': '.$this->tenant->name, route('tenants.show', $this->tenant));
            $this->addBreadcrumb(__('clients.singular').': '.$this->client->name, route('clients.show', $this->client));
            $this->addBreadcrumb(__('surveys.all'));//, route('clients.surveys.index', $this->client))   ;
        } else if ($this->survey) {
            $this->addBreadcrumb(__('tenants.singular') . ': ' . $this->tenant->name, route('tenants.show', $this->tenant));
            $this->addBreadcrumb(__('clients.singular') . ': ' . $this->client->name, route('clients.show', $this->client));
            $this->addBreadcrumb(__('surveys.all'), route('tenants.surveys.index', $this->tenant));
        } else if ($this->client) {
            $this->addBreadcrumb(__('tenants.singular') . ': ' . $this->tenant->name, route('tenants.show', $this->tenant));
            $this->addBreadcrumb(__('clients.singular') . ': ' . $this->client->name, route('clients.show', $this->client));
            $this->addBreadcrumb(__('surveys.all'), route('tenants.surveys.index', $this->tenant));
        } else if ($this->tenant) {
            $this->addBreadcrumb(__('tenants.singular') . ': ' . $this->tenant->name, route('tenants.show', $this->tenant));
            $this->addBreadcrumb(__('surveys.all'), route('tenants.surveys.index', $this->tenant));

        } else {
            $this->addBreadcrumb(__('surveys.all'));//, route('client.surveys.index', $this->client));
        }
        return view('livewire.response.list-resource', [
            'breadcrumbs' => $this->getBreadcrumbs(),
            'title' => __('responses.plural'),
            'subtitle' => __('responses.description'),
            'survey' => $this->survey,
            'updateUrl' => null
        ]);
    }
}

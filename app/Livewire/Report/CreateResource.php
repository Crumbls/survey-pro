<?php

namespace App\Livewire\Report;

use App\Livewire\Contracts\HasTenant;
use App\Models\Client;
use App\Models\Collector;
use App\Models\Collector as Model;
use App\Models\Report;
use App\Models\Response;
use App\Models\Survey;
use App\Models\Tenant;
use App\Models\TenantUserRole;
use App\Models\User;
use App\Traits\HasBreadcrumbs;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Get;
use Filament\Forms\Set;
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
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Livewire\Component;
use Filament\Tables\Actions\CreateAction;
use Filament\Forms\Form;
use Livewire\Livewire;
use Livewire\WithUrlParams;

class CreateResource extends Component implements HasForms {
use HasBreadcrumbs,
    InteractsWithForms;

    public ?Client $client = null;
    public ?Collector $collector = null;
    public ?Survey $survey = null;
    public ?Tenant $tenant = null;

    public ?array $data = [];

    public function mount() {
        abort_if(!Gate::allows('create', Report::class), 403);

        $user = auth()->user();

        if ($this->collector) {
            $this->data['collector_ids'] = [$this->collector->getKey()];
            $this->survey = $this->collector->survey;
        } else {
            /**
             * Find collectors that have responses.
             */
            $total = Collector::whereIn('survey_id',
                        Survey::whereIn('tenant_id',
                            $user
                                ->tenants()
                                ->select('tenants.id')
                        )
                        ->select('surveys.id')
                    )->whereIn('id', Response::select('collector_id'))
                ->take(2)
                ->get();
            if ($total->count() == 1) {
                $this->collector = $total->first->getKey();
                $this->data['collector_ids'] = [$this->collector->getKey()];
                $this->survey = $this->collector->survey;
            }
        }

        if ($this->survey) {
            $this->data['survey_id'] = $this->survey->getKey();
            $this->client = $this->survey->client;
        } else {
            dd(__LINE__);
        }

        if ($this->client) {
            $this->data['client_id'] = $this->client->getKey();
            $this->tenant = $this->client->tenant;
        } else {
            dd(__LINE__);
        }

        if ($this->tenant) {
            $this->data['tenant_id'] = $this->tenant->getKey();
        } else {
            dd($user->tenants);
        }

        /**
         * Add breadcrumbs.
         */

        $this->addBreadcrumb(trans('tenants.all'));

        $this->addBreadcrumb('Create Survey');
//dd($this->data);
        $this->form->fill($this->data);
    }


    protected function getRecord() : \Illuminate\Database\Eloquent\Model {
        if (isset($this->_survey)) {
            return $this->_survey;
        }

        $user = request()->user();


        $this->_survey = Survey::where('uuid', $this->surveyId)
            ->whereIn('tenant_id', $user->tenants()->select('tenants.id'))
            ->where('surveys.uuid', $this->surveyId)
            ->take(1)
            ->first();

        return $this->_survey;
    }

    protected function getTableQuery()
    {
        $record = request()->record;

        $this->surveyId = $record;

        if (!$record) {
            dd(__LINE__);
        } else {

            abort_if(!Str::of($record)->isUuid(), 404);

            $user = request()->user();

            $record = Survey::where('uuid', $record)
                ->whereIn('tenant_id', $user->tenants()->select('tenants.id'))
                ->where('surveys.uuid', $record)
                ->take(1)
                ->firstOrFail();

            return $record->reports()->getQuery();
        }

        dd(__LINE__);


        dd($record);


        return Collector::query()
            ->whereIn('tenant_id', request()->user()->tenants()->select('tenants.id'));
    }
    public function form(Form $form): Form
    {
        $user = request()->user();

        $surveys = null;


        if ($this->tenant) {
            $surveys = $this->tenant->surveys->pluck('title','id');
        } else if (Gate::allows('viewAny', Survey::class)) {
            $surveys = Survey::all()->pluck('title','id');
        } else {
            $surveys = Survey::whereIn('tenant_id', $user->tenants()->select('id'))->get()->pluck('title','id');
        }

        $collectors = null;

        if ($this->tenant) {
            $collectors = Collector::whereIn('survey_id', $this->tenant->surveys()->select('id'))->get()->pluck('name', 'id');
        } else if (Gate::allows('viewAny', Report::class)) {
            $collectors = Collector::all()->pluck('name', 'id');
        } else {
            /**
             * This needs to clean up and only show them by survey, right?
             */
            dd(__LINE__);

            $collectors = Collector::whereIn('survey_id', $this->tenant->surveys()
                ->whereIn('tenant_id', TenantUserRole::where('user_id', auth()->id())->select('tenant_id'))
                ->select('id'))->get()->pluck('name', 'id');

        }

        return $form
            ->schema([
                Select::make('tenant_id')
                    ->label(__('tenants.singular'))
                    ->options(function () {
                        if ($this->tenant) {
                            return [$this->tenant->getKey() => $this->tenant->name];
                        }
                        return request()->user()->tenants->pluck('name', 'id');
                    })->hidden(function() {
                        return isset($this->tenant) && $this->tenant;
                    })
                    ->live()  // Makes the field reactive
                    ->afterStateUpdated(function (Set $set) {
                        // Clear the dependent field when parent changes
                        $set('client_id', null);
                        $set('survey_id', null);
                        $set('collector_ids', null);
                    })
                    ->required(),


                Select::make('client_id')
                    ->label(__('clients.singular'))
                    ->options(function (Get $get) {
                        if ($this->client) {
                            return [$this->client->getKey() => $this->client->name];
                        }
                        $tenantId = $get('tenant_id');
                        if (!$tenantId) {
                            return [];
                        }
                        return Client::where('tenant_id', $tenantId)
                            ->orderBy('clients.name','asc')
                            ->pluck('name','id');
                    })->hidden(function() {
                        return isset($this->client) && $this->client;
                    })
                    ->live()  // Makes the field reactive
                    ->afterStateUpdated(function (Set $set) {
                        // Clear the dependent field when parent changes
                        $set('survey_id', null);
                        $set('collector_ids', null);
                    })
                    ->required(),

                Select::make('survey_id')
                    ->label('Survey')
                    ->options(function (Get $get) {
                        $clientId = $get('client_id');
                        if (!$clientId) {
                            return [];
                        }

                        return Survey::query()
                            ->where('client_id', $clientId)
                            ->whereIn('surveys.id',
                                Collector::whereRaw('1=1')
                                    ->whereIn('collectors.id',
                                        Response::select('collector_id')
                                        /**
                                         * TODO: Revise this query to make it simpler to speed up down the road.
                                         */
                                    )
                                    ->select('survey_id')
                            )
                            ->get()
                            ->pluck('title', 'id');
                    })
                    ->live()  // Makes the field reactive
                    ->afterStateUpdated(function (Set $set) {
                        // Clear the dependent field when parent changes
                        $set('collector_ids', null);
                    })
                    ->hidden(function() {
                        return isset($this->survey) && $this->survey;
                    })
                    ->required(),

                Select::make('collector_ids')
                    ->label(trans('collectors.plural'))
                    ->options(function (Get $get) {
                        $surveyId = $get('survey_id');

                        if (!$surveyId) {
                            return [];
                        }
                        return Collector::query()  // Assuming 'Collector' is your links model
                            ->where('survey_id', $surveyId)
                            ->pluck('name', 'id');
                    })
                    ->multiple()
                    ->required()
                    ->disabled(fn (Get $get): bool => !$get('survey_id'))
                    ->live(),
                TextInput::make('title')
                    ->required(),
            ])
            ->statePath('data');
    }

    public function create(): void
    {
        $user = request()->user();

        $data = $this->form->getState();

        $data['user_id'] = $user?->getKey();

        if (!array_key_exists('survey_id', $data) && $this->survey?->getKey()) {
            $data['survey_id'] = $this->survey->getKey();
            $data['client_id'] = $this->survey->client_id;
        } else if (!isset($data['client_id'])) {
             $survey = Survey::findOrFail($data['survey_id']);
             $data['client_id'] = $survey->client_id;
        }

        $record = Report::create($data);

        Notification::make()
            ->title(__('reports.created'))
            ->success()
            ->send();

        $this->redirectRoute('reports.edit', $record);
    }

    public function render(): View
    {
        return view('livewire.report.create-resource', [
            'breadcrumbs' => $this->getBreadcrumbs(),
        ]);
    }

}

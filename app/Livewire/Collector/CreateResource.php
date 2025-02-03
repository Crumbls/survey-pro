<?php

namespace App\Livewire\Collector;

use App\Models\Client;
use App\Models\Collector;
use App\Models\Response;
use App\Models\Survey;
use App\Models\Tenant;
use App\Models\User;
use App\Traits\HasBreadcrumbs;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
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


class CreateResource extends Component implements HasForms {
    use HasBreadcrumbs,
        InteractsWithForms;

    public array $data = [];

    public ?Tenant $tenant = null;
    public ?Client $client = null;
    public ?Survey $survey = null;

    public function mount() {
        abort_if(!Gate::allows('create', Collector::class), 403);

        $user = request()->user();

        if ($this->survey) {
            $this->client = $this->survey->client;
            $this->data['survey_id'] = $this->survey->getKey();
        }

        if ($this->client) {
            $this->tenant = $this->client->tenant;
            $this->data['client_id'] = $this->client->getKey();
        }

        if ($this->tenant) {
            $this->data['tenant_id'] = $this->tenant->getKey();
        }

        $this->addBreadcrumb('Create Survey');

        $this->form->fill($this->data);
    }



    public function form(Form $form): Form
    {
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
                    })
                    ->hidden(function() {
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
                        if ($this->survey) {
                            return [$this->survey->getKey() => $this->survey->title];
                        }

                        $clientId = $get('client_id');
                        if (!$clientId) {
                            return [];
                        }
                        return Survey::query()
                            ->where('client_id', $clientId)
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
                TextInput::make('reference')
                    ->prefix('/r/')
                    ->required()
                    ->maxLength(250)
                    ->regex('/^[a-zA-Z0-9\-]+$/')
//                    ->unique('collectors', 'unique_code')
                    ->unique('collectors', 'unique_code', modifyRuleUsing: fn ($rule, $state) => $rule->where('unique_code', $state))
                    ->live(onBlur: true)
                    ->afterStateUpdated(function ($state, callable $set) {
                        if ($state) {
                            $processed = preg_replace('/\s+/', '-', trim($state)); // Replace spaces with hyphens
                            $processed = preg_replace('/\-+/', '-', $processed);   // Remove duplicate hyphens
                            $processed = preg_replace('/^-+|-+$/', '', $processed); // Remove leading/trailing hyphens
                            $processed = preg_replace('/[^a-zA-Z0-9\-]/', '', $processed); // Remove invalid chars
                            $set('reference', $processed);
                        }
                    }),

                TextInput::make('goal')
                    ->required()
                    ->numeric()
                    ->minValue(0)
                    ->maxValue(1000000)
                    ->integer()
                    ->label('Target Response Count')

            ])
            ->statePath('data');

    }

    public function create(): void
    {
        $user = request()->user();

        $data = $this->form->getState();

        if ($this->survey) {
            $this->client = $this->survey->client;
            $data['survey_id'] = $this->survey->getKey();
        }

        if ($this->client) {
            $this->tenant = $this->client->tenant;
            $data['client_id'] = $this->client->getKey();
        }

        if ($this->tenant) {
            $data['tenant_id'] = $this->tenant->getKey();
        }

        $data['user_id'] = $user?->getKey();

        if (!array_key_exists('survey_id', $data) || !$data['survey_id']) {
            abort_if(!isset($this->survey), 500);
            $data['survey_id'] = $this->survey->getKey();
        }

        $data['type'] = 'url';

        if (!array_key_exists('name', $data) || !$data['name']) {
            $data['name'] = $data['reference'];
        }

        $data['configuration'] = [];

        $data['unique_code'] = $data['reference'];

        $data['status'] = 'open';

        $record = Collector::create($data);

        Notification::make()
            ->title(__('collectors.created'))
            ->success()
            ->send();

        $this->redirectRoute('surveys.collectors.index', $record->survey);
    }

    public function render(): View
    {
        return view('livewire.create-resource', [
            'breadcrumbs' => $this->getBreadcrumbs(),
            'title' => __('collectors.create'),
            'subtitle' => __('collectors.description'),
            'cancelUrl' => $this->survey ? route('surveys.collectors.index', $this->survey) : route('surveys.index'),
            'createText' => __('collectors.create')
        ]);
    }
}

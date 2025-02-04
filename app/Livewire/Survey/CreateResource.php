<?php

namespace App\Livewire\Survey;

use App\Livewire\Contracts\HasTenant;
use App\Models\Client;
use App\Models\Collector;
use App\Models\Collector as Model;
use App\Models\Report;
use App\Models\Survey;
use App\Models\Tenant;
use App\Models\User;
use App\Traits\HasBreadcrumbs;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
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
use Livewire\WithUrlParams;
use Silber\Bouncer\BouncerFacade;

class CreateResource extends Component implements HasForms {

    use HasBreadcrumbs,
        InteractsWithForms;

    public $data = [];

    public ?Client $client = null;
    public ?Tenant $tenant = null;

    public function mount() {
        if ($this->client) {
            $this->tenant = $this->client->tenant;
        }

        abort_if(!Gate::allows('create', Survey::class), 403);
        $user = request()->user();

        if ($this->client) {
            $this->tenant = $this->client->tenant;
            $this->addBreadcrumb(__('tenants.singular').': '.$this->tenant->name, route('tenants.show', $this->tenant));
            $this->addBreadcrumb(__('clients.singular').': '.$this->client->name, route('clients.show', $this->client));
            $this->addBreadcrumb(__('surveys.create'))   ;
        } else if ($this->tenant) {
            $this->addBreadcrumb(__('tenants.singular').': '.$this->tenant->name, route('tenants.show', $this->tenant));
            $this->addBreadcrumb(__('surveys.create'), route('tenants.surveys.index', $this->tenant));
        } else {
            $this->addBreadcrumb(__('surveys.create'));//, route('client.surveys.index', $this->client));
        }

        $this->form->fill();
    }



    public function form(Form $form): Form
    {
        $user = request()->user();

        if (!$user) {
            abort(500);
        }

        /**
         * Only show tenants with clients.
         */
        $tenants = null;
        $clients = null;

        if ($this->tenant) {
            $tenants = collect([$this->tenant]);
        } else {
            $tenants = $user
                ->tenants()
                ->whereIn('tenants.id', Client::select('tenant_id'))
//                ->whereIn($user->clients()->select('clients.tenant_id'))
                ->select('tenants.name','tenants.id')
                ->orderBy('tenants.name','asc')
                ->get();
        }

        if ($this->client) {
            $clients = collect([$this->client]);
        } else if ($tenants->count() == 1) {
            $clients = $tenants
                ->first()
                ->clients()
                ->orderBy('name','asc')
                ->get();
        } else {
            $clients = collect([]);
        }

        $tenants = $tenants->pluck('name', 'id');

        $hasSingleClient = $clients->count() === 1;

        if ($hasSingleClient) {
            $this->data['client_id'] = $clients->first()->id;
            $this->client = $clients->first();
        }

        $clients = $clients->pluck('name', 'id');

        $hasSingleTenant = $tenants->count() === 1;

        if ($hasSingleTenant) {
            $this->data['tenant_id'] = $tenants->keys()->first();
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
                    })
                    ->hidden(function() {
                        return isset($this->tenant) && $this->tenant;
                    })
                    ->live()  // Makes the field reactive
                    ->afterStateUpdated(function (Set $set) {
                        // Clear the dependent field when parent changes
                        $set('client_id', null);
                        $set('survey_id', null);
                    })
                    ->required(),

                Select::make('client_id')
                    ->label(__('clients.singular'))
                    ->options(function (Get $get) {
                        if ($this->client) {
                            return [$this->client->getKey() => $this->client->name];
                        }
                        $tenantId = $get('tenant_id');

                        if (!$tenantId && $this->tenant) {
                            $tenantId = $this->tenant->getKey();
                        }

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
                    })
                    ->required(),

                TextInput::make('title')
                    ->required(),
                Textarea::make('description')
                    ->label('Description (Optional)')
            ])
            ->statePath('data');

    }

    public function create(): void
    {
        $user = request()->user();
        $data = $this->form->getState();
        $data['user_id'] = $user?->getKey();

        if ($this->client) {
            $data['client_id'] = $this->client->getKey();
        }

        if ($this->tenant) {
            $data['tenant_id'] = $this->tenant->getKey();
        }

        $record = Survey::create($data);

        Notification::make()
            ->title(__('surveys.created'))
            ->success()
            ->send();

        $this->redirectRoute('surveys.edit', $record);
    }

    public function render(): View
    {
        return view('livewire.create-resource', [
            'breadcrumbs' => $this->getBreadcrumbs(),
            'title' => __('surveys.create'),
            'subtitle' => __('surveys.description')
        ]);
    }

}

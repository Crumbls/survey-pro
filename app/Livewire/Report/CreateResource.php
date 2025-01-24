<?php

namespace App\Livewire\Report;

use App\Livewire\Contracts\HasTenant;
use App\Models\Collector;
use App\Models\Collector as Model;
use App\Models\Report;
use App\Models\Survey;
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
    HasTenant,
    InteractsWithForms;


    public $surveyId;
    public $collectorId;

    private \Illuminate\Database\Eloquent\Model $_survey;

    public ?array $data = [];

    public function mount() {
        abort_if(!Gate::allows('create', Report::class), 403);

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
            $this->addBreadcrumb('All Centers');
        }

        $this->addBreadcrumb('Create Survey');

        $this->form->fill();
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
        $tenant = $this->getTenant();

        $surveys = null;

        if ($tenant) {
            $surveys = $tenant->surveys->pluck('title','id');
        } else if (Gate::allows('viewAny', Survey::class)) {
            $surveys = Survey::all()->pluck('title','id');
        } else {
            $surveys = Survey::whereIn('tenant_id', $user->tenants()->select('id'))->get()->pluck('title','id');
        }

        $collectors = null;

        if ($tenant) {
            $collectors = Collector::whereIn('survey_id', $tenant->surveys()->select('id'))->get()->pluck('name', 'id');
        } else if (Gate::allows('viewAny', Report::class)) {
            $collectors = Collector::all()->pluck('name', 'id');
        } else {
            /**
             * This needs to clean up and only show them by survey, right?
             */
            $collectors = Collector::whereIn('survey_id', $tenant->surveys()
                ->whereIn('tenant_id', TenantUserRole::where('user_id', auth()->id())->select('tenant_id'))
                ->select('id'))->get()->pluck('name', 'id');

        }
//dd($surveys);
        return $form
            ->schema([
                Select::make('survey_id')
                    ->label('Survey')
                    ->options(function () {
                        return Survey::query()
                            ->whereIn('tenant_id', auth()->user()->tenants->pluck('id'))
                            ->pluck('title', 'id');
                    })
                    ->live()  // Makes the field reactive
                    ->afterStateUpdated(function (Set $set) {
                        // Clear the dependent field when parent changes
                        $set('collector_ids', null);
                    })
                    ->required(),

                Select::make('collector_ids')
                    ->label('Collector')
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
//dd($data);
        $record = Report::create($data);

        /**
         * TODO: Add in notification?
         */

        $this->redirectRoute('reports.show', $record);
    }

    public function render(): View
    {
        return view('livewire.report.create-resource', [
            'breadcrumbs' => $this->getBreadcrumbs(),
            'tenant' => $this->getTenant()
        ]);
    }

}

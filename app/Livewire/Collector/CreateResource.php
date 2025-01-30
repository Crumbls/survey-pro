<?php

namespace App\Livewire\Collector;

use App\Models\Collector;
use App\Models\Collector as Model;
use App\Models\Survey;
use App\Models\User;
use App\Traits\HasBreadcrumbs;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
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

    public array $data;
    public ?string $surveyId;
    public ?Survey $survey;

    public function mount() {
        abort_if(!Gate::allows('create', Collector::class), 403);

        $user = request()->user();

        if (isset($this->surveyId)) {
            $this->survey = Survey::where('uuid', $this->surveyId)
                ->whereIn('tenant_id', $user->tenants()->select('tenants.id'))
                ->firstOrFail();
        }

/*
        if ($tenant) {
            $this->addBreadcrumb('Center: '.$tenant->name, route('tenants.show', $tenant));
        } else {
            $this->addBreadcrumb('All Centers');
        }

*/
        $this->addBreadcrumb('Create Survey');

        $this->form->fill();
    }



    public function form(Form $form): Form
    {
        $user = request()->user();

        if (!$user) {
            abort(500);
        }


        $surveys = isset($this->survey) && $this->survey ? collect([$this->survey])->pluck('title','id') :
            Survey::whereIn('tenant_id', $user->tenants()->select('tenants.id'))
                ->get()
                ->filter(function(Survey $record) {
                    return $record->getQuestionCount();
                })
                ->pluck('title','id');

        $hasSingleSurvey = $surveys->count() === 1;

        if ($hasSingleSurvey) {
            $this->data['survey_id'] = $surveys->keys()->first();
        }

        return $form
            ->schema([
                Select::make('survey_id')
                    ->options($surveys)
                    ->required()
                    ->hidden($hasSingleSurvey)
                    ->label('Survey'),
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

        $record = Collector::create($data);

        /**
         * TODO: Add in notification?
         */

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

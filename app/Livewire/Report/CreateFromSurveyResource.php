<?php

namespace App\Livewire\Report;

use App\Models\Collector;
use App\Models\Collector as Model;
use App\Models\Report;
use App\Models\Survey;
use App\Models\User;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
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
use Illuminate\Support\Str;
use Livewire\Component;
use Filament\Tables\Actions\CreateAction;
use Filament\Forms\Form;
use Livewire\WithUrlParams;

/**
 * @deprecated
 */
class CreateFromSurveyResource extends Component implements HasForms {

    use InteractsWithForms;


    public $surveyId;
    public $collectorId;

    private \Illuminate\Database\Eloquent\Model $_survey;

    public ?array $data = [];

    public function mount(string $surveyId): void
    {
        $this->surveyId = $surveyId;

        abort_if(!$this->surveyId, 404);

        abort_if(!Str::of($this->surveyId)->isUuid(), 404);

        $this->form->fill();
    }


    protected function getRecord() : \Illuminate\Database\Eloquent\Model {
        if (isset($this->_survey)) {
            return $this->_survey;
        }

        $user = request()->user();

        if (!$this->surveyId) {
            dd($this->surveyId);
        }

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
        $record = $this->getRecord();

        $collectors = null;

        if ($record) {
            $collectors = $record
                ->collectors()
                ->select('name','id')
                ->get()
                ->pluck('name', 'id');
        } else {
            /**
             * Show collectors that are part of a survey owned by a tenant wher user exists.
             */
            dd(__LINE__);
        }

        return $form
            ->schema([
                Select::make('collector_ids')
                    ->label('Survey Collectors')
                    ->multiple()
                    ->required()
                    ->options($collectors->toArray()),
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

        if (array_key_exists('survey_id', $data) && $data['survey_id']) {

        } else if (isset($this->surveyId)) {
            $data['survey_id'] = $this->surveyId;
        } else {
            dd(__LINE__);
        }

        if (Str::of($data['survey_id'])->isUuid()) {
            $survey = Survey::where('uuid', $data['survey_id'])
                    ->whereIn('tenant_id', $user->tenants()->select('tenants.id'))
                ->take(1)
                ->first();

            abort_if(!$survey, 404);

            $data['survey_id'] = $survey->getKey();
        }

        if (array_key_exists('collector_ids', $data) && $data['collector_ids']) {

        } else if (isset($this->collector_id)) {
            $data['collector_ids'] = [
                $this->collector_id
            ];
        } else {
            dd(__LINE__);
        }

        $record = Report::create($data);

        /**
         * TODO: Add in notification?
         */

        $this->redirectRoute('reports.edit', $record);
    }

    public function render(): View
    {
        return view('livewire.report.create-resource');
    }

}

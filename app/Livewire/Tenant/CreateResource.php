<?php

namespace App\Livewire\Tenant;

use App\Models\Collector;
use App\Models\Collector as Model;
use App\Models\Report;
use App\Models\Role;
use App\Models\Survey;
use App\Models\Tenant;
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
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Livewire\Component;
use Filament\Tables\Actions\CreateAction;
use Filament\Forms\Form;
use Livewire\WithUrlParams;
use Filament\Forms\Components\Tabs;
class CreateResource extends Component implements HasForms {

    use InteractsWithForms;

    public ?array $data = [];

    public function mount(): void
    {
        abort_if(!Gate::allows('create', Tenant::class), 403);

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


        return Model::query()
            ->whereIn('tenant_id', request()->user()->tenants()->select('tenants.id'));
    }
    public function form(Form $form): Form
    {
        $record = null;
       //$forms = Form::

        $collectors = null;
        return $form
            ->schema([
                TextInput::make('name')
                    ->required(),
            ])
            ->statePath('data');
    }

    public function create(): void
    {
        $user = request()->user();

        $data = $this->form->getState();

        abort_if(!Gate::allows('create', Tenant::class), 403);

        $record = new Tenant($data);

        $record->save();

        $role = Role::firstOrCreate(['name' => 'center-owner'], ['title' => 'Center Owner']);

        dd(__LINE__);
        $record->users()->attach($user->getKey());
        session()->flash('success', 'Center has been created.');

        $this->redirectRoute('tenants.show', $record);
    }

    public function render(): View
    {
        return view('livewire.tenant.create-resource');
    }

}

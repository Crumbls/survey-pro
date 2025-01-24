<?php

namespace App\Livewire\Response;

use App\Models\Response as Model;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;
use Filament\Forms\Form;

class EditResource extends Component implements HasForms
{
    use InteractsWithForms;

    public ?array $data = [];
    public Model $record;

    public function mount(Model $record): void
    {
        abort_if(!Gate::allows('update', $record), 403);

        $this->record = $record;
        $this->form->fill($record->toArray());
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('survey_id')
                    ->relationship('survey', 'name')
                    ->searchable()
                    ->preload()->required(),
                Select::make('collector_id')
                    ->relationship('collector', 'name')
                    ->searchable()
                    ->preload()->required(),
                MarkdownEditor::make('data')->required()
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        abort_if(!Gate::allows('update', $this->record), 403);

        $data = $this->form->getState();

        $this->record->update($data);

        session()->flash('success', 'Response has been updated.');
        $this->redirectRoute('responses.show', $this->record);
    }

    public function render(): View
    {
        return view('livewire.edit-resource');
    }
}
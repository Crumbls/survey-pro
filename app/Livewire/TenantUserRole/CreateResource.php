<?php

namespace App\Livewire\TenantUserRole;

use App\Models\TenantUserRole as Model;
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

class CreateResource extends Component implements HasForms
{
    use InteractsWithForms;

    public ?array $data = [];

    public function mount(): void
    {
        abort_if(!Gate::allows('create', Model::class), 403);
        $this->form->fill();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('tenant_id')
                    ->relationship('tenant', 'name')
                    ->searchable()
                    ->preload()->required(),
                Select::make('user_id')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->preload()->required(),
                Select::make('role_id')
                    ->relationship('role', 'name')
                    ->searchable()
                    ->preload()->required()
            ])
            ->statePath('data');
    }

    public function create(): void
    {
        abort_if(!Gate::allows('create', Model::class), 403);

        $data = $this->form->getState();
        $record = new Model($data);
        $record->save();

        session()->flash('success', 'TenantUserRole has been created.');
        $this->redirectRoute('tenantUserRoles.show', $record);
    }

    public function render(): View
    {
        return view('livewire.tenantUserRole.create-resource');
    }
}
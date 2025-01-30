<?php

namespace App\Livewire\Client;

use App\Models\Client as Model;
use App\Models\Tenant;
use App\Models\User;
use App\Traits\HasBreadcrumbs;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
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
use Filament\Forms\Components\Tabs;
class CreateResource extends Component implements HasForms {

    use HasBreadcrumbs,
        InteractsWithForms;

    public ?array $data = [];

    public ?Tenant $tenant = null;
    public function mount(): void
    {
        abort_if(!Gate::allows('create', Model::class), 403);

        if ($this->tenant && $this->tenant->getKey()) {
            $this->addBreadcrumb(trans('tenants.singular').': '.$this->tenant->name);
            $this->addBreadcrumb(trans('clients.all'), route('tenants.clients.index', $this->tenant));
            $this->addBreadcrumb(trans('clients.create'));
        } else {
            $this->addBreadcrumb(trans('tenants.all'), route('tenants.index'));
            $this->addBreadcrumb(trans('clients.all'), route('clients.index'));
        }


        $this->form->fill();
    }



    public function form(Form $form): Form
    {
        $tenants = isset($this->tenant) ? collect([$this->tenant]) : request()->user()->tenants()->select('tenants.name','tenants.id')->get();

        $tenants = $tenants->pluck('name', 'id');

        $hasSingleTenant = $tenants->count() <= 1;

        return $form
            ->schema([
                Select::make('tenant_id')
                    ->options($tenants)
                    ->required()
                    ->hidden($hasSingleTenant)
                    ->label('Center'),
                TextInput::make('name')
                    ->required(),
            ])
            ->statePath('data');
    }

    public function create(): void
    {
        abort_if(!Gate::allows('create', Model::class), 403);

        $user = request()->user();

        $data = $this->form->getState();

        if (!array_key_exists('tenant_id', $data) || !$data['tenant_id']) {
            $tenant = $user->tenants()->inRandomOrder()->first();
            abort_if(!$tenant, 403);
            $data['tenant_id'] = $tenant->getKey();
        } else {
            dd($data);
        }

        $record = new Model($data);

        $record->save();

        Notification::make()
            ->title(__('clients.created'))
            ->success()
            ->send();

        $this->redirectRoute('clients.show', $record);
    }

    public function render(): View
    {
        return view('livewire.create-resource', [
            'breadcrumbs' => $this->getBreadcrumbs(),
            'title' => __('clients.create'),
            'subtitle' => __('clients.description'),
            'cancelUrl' => $this->tenant ? route('tenants.clients.index', $this->tenant) : route('clients.index'),
            'createText' => __('clients.create')
        ]);
        return view('livewire.create-resource');
    }

}

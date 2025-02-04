<?php

namespace App\Livewire\Product;

use App\Models\Client as Model;
use App\Models\Product;
use App\Models\Tenant;
use App\Models\User;
use App\Traits\HasBreadcrumbs;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Textarea;
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

    public $logo = null;

    public ?Tenant $tenant = null;
    public function mount(): void
    {
        abort_if(!Gate::allows('create', Model::class), 403);

        if ($this->tenant && $this->tenant->getKey()) {
            $this->addBreadcrumb(trans('tenants.singular').': '.$this->tenant->name);
            $this->addBreadcrumb(trans('products.all'), route('tenants.products.index', $this->tenant));
            $this->addBreadcrumb(trans('products.create'));
        } else {
            $this->addBreadcrumb(trans('tenants.all'), route('tenants.index'));
            $this->addBreadcrumb(trans('products.all'), route('products.index'));
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
                Textarea::make('description')
                    ->required()
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
        }

        $record = new Product($data);

        $record->save();

        Notification::make()
            ->title(__('products.created'))
            ->success()
            ->send();

        $this->redirectRoute('products.show', $record);
    }

    public function render(): View
    {
        return view('livewire.create-resource', [
            'breadcrumbs' => $this->getBreadcrumbs(),
            'title' => __('products.create'),
            'subtitle' => __('products.description'),
            'cancelUrl' => $this->tenant ? route('tenants.products.index', $this->tenant) : route('products.index'),
            'createText' => __('products.create')
        ]);
    }

}

<?php

namespace App\Livewire\Survey;

use App\Livewire\Contracts\HasTenant;
use App\Models\Collector;
use App\Models\Collector as Model;
use App\Models\Report;
use App\Models\Survey;
use App\Models\User;
use App\Traits\HasBreadcrumbs;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
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

class CreateResource extends Component implements HasForms {

    use HasBreadcrumbs,
        HasTenant,
        InteractsWithForms;

    public $data = [];
    public function mount() {
        abort_if(!Gate::allows('create', Survey::class), 403);

        $tenantId = request()->tenantId;

        $this->setTenant($tenantId);

        $tenant = $this->getTenant();

        $user = request()->user();

        if (!$tenant) {
            if ($user->tenants()->count() == 1) {
                $tenant = $user->tenants()->first();
                if (!Gate::allows('viewAny', \App\Models\Survey::class)) {
                    return redirect()->route('tenants.surveys.show', $tenant);
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



    public function form(Form $form): Form
    {
        $user = request()->user();

        if (!$user) {
            abort(500);
        }

        $tenant = $this->getTenant();

        $tenants = $tenant ? collect([$tenant])->pluck('name', 'id') : $user
            ->tenants()
            ->select('tenants.name','tenants.id')
            ->get()
            ->pluck('name', 'id');

        $hasSingleTenant = $tenants->count() === 1;

        if ($hasSingleTenant) {
            $this->data['tenant_id'] = $tenants->keys()->first();
        }

        return $form
            ->schema([

                Select::make('tenant_id')
                    ->options($tenants)
                    ->required()
                    ->hidden($hasSingleTenant)
                    ->label('Center'),

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

        if (!array_key_exists('tenant_id', $data) || !$data['tenant_id']) {
            $tenant = $this->getTenant();
            abort_if(!$tenant, 500);
            $data['tenant_id'] = $tenant->getKey();
        }

        $record = Survey::create($data);

        /**
         * TODO: Add in notification?
         */

        $this->redirectRoute('surveys.edit', $record);
    }

    public function render(): View
    {
        return view('livewire.create-resource', [
            'breadcrumbs' => $this->getBreadcrumbs(),
        ]);
    }

}

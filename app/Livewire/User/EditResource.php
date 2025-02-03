<?php

namespace App\Livewire\User;

use App\Livewire\Contracts\HasTenant;
use App\Models\Client;
use App\Models\Tenant;
use App\Models\TenantUserRole;
use App\Models\User;
use App\Models\User as Model;
use App\Traits\HasBreadcrumbs;
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
use Livewire\Component;
use Filament\Tables\Actions\CreateAction;


class EditResource extends Component implements HasForms {

    use HasBreadcrumbs,
        InteractsWithForms;

    public ?User $user = null;
    public ?Client $client = null;
    public ?Tenant $tenant = null;

    public function mount() {
        abort_if(!$this->user || !$this->user->getKey(), 404);

        abort_if(!Gate::allows('update', $this->user), 403);

        if ($this->client) {
            $this->tenant = $this->client->tenant;
            $this->addBreadcrumb(__('tenants.singular').': '.$this->tenant->name, route('tenants.show', $this->tenant));
            $this->addBreadcrumb(__('clients.singular').': '.$this->client->name, route('clients.show', $this->client));
            $this->addBreadcrumb(__('users.edit'));//, route('clients.surveys.index', $this->client))   ;
        } else if ($this->tenant) {
            $this->addBreadcrumb(__('tenants.singular').': '.$this->tenant->name, route('tenants.show', $this->tenant));
            $this->addBreadcrumb(__('users.all'), route('tenants.users.index', $this->tenant));
        } else {
            $this->addBreadcrumb(__('users.edit'));//, route('client.surveys.index', $this->client));
        }
    }

    public function render(): View {

        if ($this->user->getKey() == auth()->id()) {
            dd($this->user, auth()->id());
            dd('Sorry, you can not edit your own profile here yet.');
        }

        return view('livewire.user.edit-resource', [
            'breadcrumbs' => $this->getBreadcrumbs(),
            'title' => __('users.edit'),
            'subtitle' => __('users.description'),
            'cancelUrl' => $this->tenant ? route('tenants.users.index', $this->tenant) : route('users.index'),
            'createText' => __('users.edit'),
        ]);
    }
}

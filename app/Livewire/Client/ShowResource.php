<?php

namespace App\Livewire\Client;

use App\Models\Client;
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
class ShowResource extends Component {

    use HasBreadcrumbs;

    public ?array $data = [];

    public ?Client $client = null;
    public ?Tenant $tenant = null;
    public function mount(): void
    {
        $this->tenant = $this->client->tenant;

        $this->addBreadcrumb(trans('tenants.singular').': '.$this->tenant->name, route('tenants.show', $this->tenant));
        $this->addBreadcrumb(trans('clients.singular').': '.$this->client->name);
    }



    public function render(): View
    {
        return view('livewire.client.show-resource', [
            'record' => $this->client,
            'breadcrumbs' => $this->getBreadcrumbs(),
            'title' => __('clients.singular').': '.$this->client->name,
            'subtitle' => __('clients.description'),
            'updateUrl' => Gate::allows('update', $this->client) ? route('clients.edit', $this->client) : null
        ]);
        return view('livewire.create-resource');
    }

}

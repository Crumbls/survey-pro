<?php

namespace App\Livewire\Product;

use App\Models\Client;
use App\Models\Client as Model;
use App\Models\Product;
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

    public ?Product $product = null;
    public ?Tenant $tenant = null;
    public function mount(): void
    {
        $this->tenant = $this->product->tenant;

        $this->addBreadcrumb(trans('tenants.singular').': '.$this->tenant->name, route('tenants.show', $this->tenant));
        $this->addBreadcrumb(trans('products.singular').': '.$this->product->name);
    }



    public function render(): View
    {
        return view('livewire.product.show-resource', [
            'record' => $this->product,
            'breadcrumbs' => $this->getBreadcrumbs(),
            'title' => __('products.singular').': '.$this->product->name,
            'subtitle' => __('products.description'),
            'updateUrl' => Gate::allows('update', $this->product) ? route('products.edit', $this->product) : null
        ]);
    }

}

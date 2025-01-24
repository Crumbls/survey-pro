<?php

namespace App\Livewire\TenantUserRole;

use App\Livewire\Contracts\HasTenant;
use App\Models\TenantUserRole as Model;
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
use Illuminate\Support\Facades\Gate;
use Livewire\Component;
use Filament\Tables\Actions\CreateAction;

class ListResource extends Component implements HasForms, HasTable
{
    use HasBreadcrumbs,
        HasTenant,
        InteractsWithTable,
        InteractsWithForms;

    protected $isSingle = 1;

    public function mount()
    {
        $tenantId = request()->tenantId;
        $this->setTenant($tenantId);
        $tenant = $this->getTenant();
        $user = request()->user();

        if (!$tenant && $user->tenants()->count() == 1) {
            $tenant = $user->tenants()->first();
            return redirect()->route('tenants.TenantUserRole.index', $tenant);
        }
    }

    protected function getTableQuery()
    {
        abort_if(!Gate::allows('viewAny', Model::class), 403);

        $tenant = $this->getTenant();
        $user = request()->user();

        if ($tenant) {
            $this->addBreadcrumb('Center: '.$tenant->name, route('tenants.show', $tenant));
            $this->addBreadcrumb('All TenantUserRoles');
            $this->isSingle = true;
        } else {
            $this->addBreadcrumb('All TenantUserRoles');
            $this->isSingle = false;
        }

        return Model::query()
            ->when($tenant, function ($query) use ($tenant) {
                return $query->whereHas('tenants', function ($query) use ($tenant) {
                    $query->where('tenants.id', $tenant->id);
                });
            })
            ->when(!$tenant, function ($query) use ($user) {
                return $query->whereHas('tenants', function ($query) use ($user) {
                    $query->whereIn('tenants.id', $user->tenants->pluck('id'));
                });
            });
    }

    public function table(Table $table): Table
    {
        return $table
            ->query($this->getTableQuery())
            ->columns([
                TextColumn::make('id'),
            TextColumn::make('tenant_id')
                ->label('Tenant Id'),
            TextColumn::make('user_id')
                ->label('User Id'),
            TextColumn::make('role_id')
                ->label('Role Id'),
            TextColumn::make('created_at')
                ->label('Created At')
                ->dateTime(),
            TextColumn::make('updated_at')
                ->label('Updated At')
                ->dateTime()
            ])
            ->headerActions([
                CreateAction::make('create')
                    ->label('Create New')
                    ->url(function() {
                        $tenant = $this->getTenant();
                        return $tenant ? route('tenants.TenantUserRole.create', $tenant) : route('TenantUserRole.create');
                    })
                    ->button()
                    ->color('custom')
                    ->extraAttributes([
                        'class' => 'bg-primary-600 hover:bg-primary-700'
                    ])
                    ->visible(fn() => Gate::allows('create', Model::class))
            ])
            ->actions([
                ActionGroup::make([
                    Action::make('edit')
                        ->label('Edit')
                        ->icon('heroicon-m-pencil-square')
                        ->url(fn ($record) => route('TenantUserRole.edit', $record))
                        ->color('custom')
                        ->extraAttributes([
                            'class' => 'text-primary-600 hover:text-primary-700'
                        ])
                ])
                ->label('Actions')
                ->icon('heroicon-m-ellipsis-vertical')
                ->size('sm')
                ->color('custom')
                ->extraAttributes([
                    'class' => 'text-primary-600 hover:text-primary-700'
                ])
            ]);
    }

    public function render(): View
    {
        return view('livewire.TenantUserRole.list-resource', [
            'breadcrumbs' => $this->getBreadcrumbs()
        ]);
    }
}
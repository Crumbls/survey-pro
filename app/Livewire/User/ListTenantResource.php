<?php

namespace App\Livewire\User;

use App\Livewire\Contracts\HasTenant;
use App\Models\Client;
use App\Models\Role;
use App\Models\Tenant;
use App\Models\TenantUserRole;
use App\Models\User;
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


class ListTenantResource extends Component implements HasForms, HasTable {
    use
        InteractsWithTable,
        InteractsWithForms;

    protected $isSingle = 1;

    public User $user;

    public function mount() {
    }

    protected function getTableQuery()
    {
        return $this->user->tenants()
            ->withPivot(['role', 'role_id'])
            ->getQuery();

    }

    public function table(Table $table): Table {
        $user = request()->user();
        $tenantCount = $user->tenants()->count();
    return $table
        ->query($this->getTableQuery())
        ->columns(array_filter([
            TextColumn::make('name'),
            TextColumn::make('role_id')
                ->label(trans('roles.singular'))
                ->formatStateUsing(function (string $state): string {
                    return $state ? static::getRole($state) : 'Unknown';
                }),

        ]))
        ->headerActions([
            // Add a custom button in the header
        ])
        ->filters([
            // ...
        ])
        ->actions([
            ActionGroup::make([
                /*
                Action::make('edit')
                    ->label('Edit User')
                    ->icon('heroicon-m-pencil-square')
                    ->url(fn ($record) => route('users.edit', $record))
                    ->color('custom')
                    ->extraAttributes([
                        'class' => 'text-primary-600 hover:text-primary-700' // Add hover state
                    ]),
                */
                Action::make('collectors')
                    ->label('Collectors')
                    ->icon('heroicon-m-pencil-square')
                    ->url(fn ($record) => route('surveys.collectors.index', $record))
                    ->color('custom')
                    ->extraAttributes([
                        'class' => 'text-primary-600 hover:text-primary-700' // Add hover state
                    ]),
                Action::make('reports')
                    ->label('Reports')
                    ->icon('heroicon-m-pencil-square')
                    ->url(fn ($record) => route('surveys.reports.index', $record))
                    ->color('custom')
                    ->extraAttributes([
                        'class' => 'text-primary-600 hover:text-primary-700' // Add hover state
                    ]),

            ])
                ->label('Actions')
                ->icon('heroicon-m-ellipsis-vertical')
                ->size('sm')
                ->color('custom')
                ->extraAttributes([
                    'class' => 'text-primary-600 hover:text-primary-700' // Add hover state
                ])
        ])
        ->bulkActions([
            // ...
        ]);
}

    public function render(): View {
        return view('livewire.list-resource', [
            'title' => __('users.all'),
            'subtitle' => __('users.description'),
//            'cancelUrl' => $this->tenant ? route('tenants.users.index', $this->tenant) : route('users.index'),
            'createText' => __('users.create')
        ]);
    }

    public static function getRole(int $roleId) : ?string {
        return once(function() use ($roleId) {
            return Role::find($roleId)?->title;
        });
    }
}

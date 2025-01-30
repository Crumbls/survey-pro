<?php

namespace App\Livewire\Tenant;

use App\Livewire\Contracts\HasTenant;
use App\Models\Tenant;
use App\Models\Tenant as Model;
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

    public function mount() {

        $user = request()->user();

        if ($user->tenants()->count() == 1) {
            return redirect()->route('tenants.show', $user->tenants->first());
        }

        $this->addBreadcrumb(__('tenants.all'));

    }

    protected function getTableQuery()
    {
        $user = request()->user();

        return $user->tenants()
            ->getQuery();
    }

    public function table(Table $table): Table
    {
        return $table
            ->query($this->getTableQuery())
            ->recordUrl(function($record) {
                return Gate::allows('view', $record) ? route('tenants.show', $record) : null;
            })
            ->columns([
                TextColumn::make('name')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()

                    ->toggleable(isToggledHiddenByDefault: true)
                ,
                TextColumn::make('users_count')
                    ->counts('users')
                    ->label('Users')

                    ->url(fn (Model $record): string => route('tenants.users.index', $record)),
                TextColumn::make('surveys_count')
                    ->counts('surveys')
                    ->label('Surveys')

                    ->url(fn (Model $record): string => route('tenants.surveys.index', $record))
            ])
            ->headerActions([
                /*
                CreateAction::make('create')
                    ->label('Create New')
                    ->url(route('tenants.create'))
                    ->button()
                    ->color('custom')
                    ->extraAttributes([
                        'class' => 'bg-primary-600 hover:bg-primary-700'
                    ])
                    ->visible(fn (): bool => auth()->user()->can('create', Model::class))
                */
            ])
            ->filters([
                // Add any filters you need
            ])
            ->actions([
                ActionGroup::make([
                    Action::make('edit')
                        ->label('Edit')
                        ->icon('heroicon-m-pencil-square')
                        ->url(fn (Model $record) => route('tenants.edit', $record))
                        ->color('custom')
                        ->extraAttributes([
                            'class' => 'text-primary-600 hover:text-primary-700'
                        ])
                        ->visible(fn (Model $record): bool => auth()->user()->can('update', $record)),
/*
                    Action::make('users')
                        ->label('Manage Users')
                        ->icon('heroicon-m-users')
                        ->url(fn (Model $record) => route('tenants.users.index', $record))
                        ->color('custom')
                        ->extraAttributes([
                            'class' => 'text-primary-600 hover:text-primary-700'
                        ])
                        ->visible(fn (Model $record): bool => auth()->user()->can('update', $record)),
*/
                    Action::make('delete')
                        ->label('Delete')
                        ->icon('heroicon-m-trash')
                        ->requiresConfirmation()
                        ->color('custom')
                        ->extraAttributes([
                            'class' => 'text-red-600 hover:text-red-700'
                        ])
                        ->action(fn (Model $record) => $record->delete())
                        ->visible(fn (Model $record): bool => auth()->user()->can('delete', $record))
                ])
                    ->label('Actions')
                    ->icon('heroicon-m-ellipsis-vertical')
                    ->size('sm')
                    ->color('custom')
                    ->extraAttributes([
                        'class' => 'text-primary-600 hover:text-primary-700'
                    ])
            ])
            ->bulkActions([
                // Add bulk actions if needed
            ]);
    }

    public function render(): View
    {
        return view('livewire.list-resource', [
            'breadcrumbs' => $this->getBreadcrumbs(),
            'title' => __('tenants.all'),
            'subtitle' => __('tenants.description')
        ]);
    }
}

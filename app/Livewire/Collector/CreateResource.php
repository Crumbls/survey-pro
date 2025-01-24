<?php

namespace App\Livewire\Collector;

use App\Models\Collector as Model;
use App\Models\Survey;
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
use Illuminate\Support\Str;
use Livewire\Component;
use Filament\Tables\Actions\CreateAction;


class CreateResource extends Component implements HasForms, HasTable {
    use HasBreadcrumbs,
        InteractsWithTable,
        InteractsWithForms;

    protected ?string $recordId = null;

    protected function getTableQuery()
    {
        $record = request()->record;

        $this->recordId = $record;

        $this->addBreadcrumb('All Surveys', route('surveys.index'));

        if (!$record) {
            dd(__LINE__);
        } else {
            abort_if(!Str::of($record)->isUuid(), 404);

            $user = request()->user();

            $record = Survey::where('uuid', $record)
                ->whereIn('tenant_id', $user->tenants()->select('tenants.id'))
                ->where('surveys.uuid', $record)
                ->take(1)
                ->firstOrFail();

            $this->addBreadcrumb($record->uuid, route('surveys.show', $record));



            return $record->collectors()->getQuery();
        }

            dd(__LINE__);


        dd($record);


        return Model::query()
            ->whereIn('tenant_id', request()->user()->tenants()->select('tenants.id'));
    }

    public function table(Table $table): Table {
        $user = request()->user();
        $tenantCount = $user->tenants()->count();
    return $table
        ->query($this->getTableQuery())
        ->columns([
            TextColumn::make('name'),
            TextColumn::make('type'),
            TextColumn::make('status'),
        ])
        ->recordUrl(function (Model $record) {
                return $record->status == 'open' && $record->type == 'url' ? url('/r/'.$record->unique_code) : '';
        })
        ->headerActions([
            // Add a custom button in the header
            CreateAction::make('create')
                ->label('Create New')
                ->url(function() : string {
                    return $this->recordId ? route('surveys.collectors.create', $this->recordId) : '###';
                })
                ->button()
                ->color('custom') // Use custom color
                ->extraAttributes([
                    'class' => 'bg-primary-600 hover:bg-primary-700' // Add hover state
                ])
                ->visible(fn (): bool => true)
        ])
        ->filters([
            // ...
        ])
        ->actions([
            ActionGroup::make([
                Action::make('view')
                    ->label('View')
                    ->icon('heroicon-m-pencil-square')
                    ->url(function ($record) {
                        return $record->status == 'open' && $record->type == 'url' ? route('collector.show', $record->unique_code) : '';
                    })
                    ->color('custom')
                    ->extraAttributes([
                        'class' => 'text-primary-600 hover:text-primary-700' // Add hover state
                    ]),
                Action::make('open')
                    ->label('Open')
                    ->icon('heroicon-m-pencil-square')
                    ->color('custom')
                    ->extraAttributes([
                        'class' => 'text-primary-600 hover:text-primary-700' // Add hover state
                    ])
                    ->action(function(Model $record) {
                        $record->update([
                            'status' => 'open'
                        ]);
                    }),
                Action::make('close')
                    ->label('Close')
                    ->icon('heroicon-m-pencil-square')
                    ->url(fn ($record) => route('surveys.edit', $record))
                    ->color('custom')
                    ->extraAttributes([
                        'class' => 'text-primary-600 hover:text-primary-700' // Add hover state
                    ])
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
        return view('livewire.collector.list-resource', [
            'breadcrumbs' => $this->getBreadcrumbs()
        ]);
    }
}

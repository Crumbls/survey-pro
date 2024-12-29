<?php

namespace App\Livewire\Collector;

use App\Models\Collector as Model;
use App\Models\Survey;
use App\Models\User;
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


class ListResource extends Component implements HasForms, HasTable {
    use InteractsWithTable;
    use InteractsWithForms;

    protected function getTableQuery()
    {
        $record = request()->record;
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
                ->url(route('surveys.create'))
                ->button()
                ->color('custom') // Use custom color
                ->extraAttributes([
                    'class' => 'bg-teal-600 hover:bg-teal-700' // Add hover state
                ])
                ->visible(fn (): bool => true)
        ])
        ->filters([
            // ...
        ])
        ->actions([
            ActionGroup::make([
                Action::make('edit')
                    ->label('Edit Survey')
                    ->icon('heroicon-m-pencil-square')
                    ->url(function ($record) {
                        return $record->status == 'open' && $record->type == 'url' ? url('/r/'.$record->unique_code) : '';
                    })
                    ->color('custom')
                    ->extraAttributes([
                        'class' => 'text-teal-600 hover:text-teal-700' // Add hover state
                    ]),
                Action::make('open')
                    ->label('Open')
                    ->icon('heroicon-m-pencil-square')
                    ->color('custom')
                    ->extraAttributes([
                        'class' => 'text-teal-600 hover:text-teal-700' // Add hover state
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
                        'class' => 'text-teal-600 hover:text-teal-700' // Add hover state
                    ])
            ])
                ->label('Actions')
                ->icon('heroicon-m-ellipsis-vertical')
                ->size('sm')
                ->color('custom')
                ->extraAttributes([
                    'class' => 'text-teal-600 hover:text-teal-700' // Add hover state
                ])
        ])
        ->bulkActions([
            // ...
        ]);
}

    public function render(): View {
        return view('livewire.survey.list-resource');
    }
}

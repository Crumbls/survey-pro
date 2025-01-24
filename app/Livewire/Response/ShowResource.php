<?php

namespace App\Livewire\Response;

use App\Models\Response as Model;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Concerns\InteractsWithInfolists;
use Filament\Infolists\Contracts\HasInfolists;
use Filament\Infolists\Infolist;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;

class ShowResource extends Component implements HasForms, HasInfolists
{
    use InteractsWithForms;
    use InteractsWithInfolists;

    public Model $record;

    public function mount(Model $record): void
    {
        abort_if(!Gate::allows('view', $record), 403);
        $this->record = $record;
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->record($this->record)
            ->schema([
                TextEntry::make('id')
                    ->label('Id'),
                TextEntry::make('survey.name')
                    ->label('Survey Id'),
                TextEntry::make('collector.name')
                    ->label('Collector Id'),
                TextEntry::make('data')
                    ->label('Data')
                    ->markdown(),
                TextEntry::make('created_at')
                    ->label('Created At')
                    ->dateTime(),
                TextEntry::make('updated_at')
                    ->label('Updated At')
                    ->dateTime()
            ]);
    }

    public function render(): View
    {
        return view('livewire.response.show-resource');
    }
}
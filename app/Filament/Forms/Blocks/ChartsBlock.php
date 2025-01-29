<?php

namespace App\Filament\Forms\Blocks;

use Filament\Forms\Components\Builder\Block;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;

class ChartsBlock extends Block
{
    public static function make(string $name = 'charts'): static
    {
        return parent::make('charts')
            ->schema([
                CheckboxList::make('charts')
                    ->options([
                        'weighted' => 'Weighted Score',
                        'alignment' => 'Alignment Factor'
                    ])
                ->columnSpanFull()
            ])
            ->columns([
//                'sm' => 1,
                'default' => 1,
            ]);
    }

}

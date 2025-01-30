<?php

namespace App\Filament\Forms\Blocks;

use Filament\Forms\Components\Builder\Block;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;

class HeatmapBlock extends Block
{
    public static function make(string $name = 'heatmap'): static
    {
        return parent::make($name)
            ->schema([

            ])
            ->columns([
                'default' => 1
            ]);
    }
}

<?php

namespace App\Filament\Forms\Blocks;

use Filament\Forms\Components\Builder\Block;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;

class PistonBlock extends Block
{
    public static function make(string $name = 'pistons'): static
    {
        return parent::make($name)
            ->schema([

            ])
            ->columns([
//                'sm' => 1,
                'default' => 2,
            ]);
    }

}

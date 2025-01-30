<?php

namespace App\Filament\Forms\Blocks;

use Filament\Forms\Components\Builder\Block;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;

class CenterLogoBlock extends Block
{
    public static function make(string $name = 'center-logo'): static
    {
        return parent::make($name)
            ->schema([
            ])
            ;
    }

}

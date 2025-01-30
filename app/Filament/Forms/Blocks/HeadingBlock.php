<?php

namespace App\Filament\Forms\Blocks;

use Filament\Forms\Components\Builder\Block;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;

class HeadingBlock extends Block
{
    public static function make(string $name = 'heading'): static
    {
        return parent::make($name)
            ->schema([
                TextInput::make('content')
                    ->label('Heading')
                    ->required()
                ->columnSpanFull(),
                Select::make('level')
                    ->options([
                        'h1' => 'Heading 1',
                        'h2' => 'Heading 2',
                        'h3' => 'Heading 3',
                        'h4' => 'Heading 4',
                        'h5' => 'Heading 5',
                        'h6' => 'Heading 6',
                    ])
                    ->required(),
                Select::make('alignment')
                    ->options([
                        'text-left' => 'Left',
                        'text-center' => 'Center',
                        'text-right' => 'Right',
                    ])
                    ->default('text-left')
                    ->required()
                    ->columnSpan([
                        'sm' => 1,
                        'default' => '2'
                    ]),
            ])
            ->columns([
                'default' => 2
            ]);
    }
}

<?php

namespace App\Filament\Forms\Blocks;

use Filament\Forms\Components\Builder\Block;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;

class CylindersBlock extends Block
{
    public static function make(string $name = 'cylinder'): static
    {
        $defaultData = [
            ['cylinder' => 'Strategy', 'status' => '', 'causes' => '', 'recommendations' => ''],
            ['cylinder' => 'Structure', 'status' => '', 'causes' => '', 'recommendations' => ''],
            ['cylinder' => 'People Systems', 'status' => '', 'causes' => '', 'recommendations' => ''],
            ['cylinder' => 'Methods & Tools', 'status' => '', 'causes' => '', 'recommendations' => ''],
            ['cylinder' => 'Lateral Processes', 'status' => '', 'causes' => '', 'recommendations' => ''],
            ['cylinder' => 'Metrics', 'status' => '', 'causes' => '', 'recommendations' => ''],
            ['cylinder' => 'Cyber', 'status' => '', 'causes' => '', 'recommendations' => ''],
        ];

        return parent::make($name)
            ->schema([
                Repeater::make('rows')
                    ->schema([
                        TextInput::make('cylinder')
                            ->label('Cylinder')
//                            ->disabled()
  //                          ->dehydrated()
                            ->required(),
                        Select::make('status')
                            ->label('Pain or Positive')
                            ->options([
                                'pain' => 'Pain Point',
                                'positive' => 'Positive',
                            ]),
                        Textarea::make('causes')
                            ->label('Causes')
                            ->rows(2),
                        Textarea::make('recommendations')
                            ->label('Recommendations')
                            ->rows(2),
                    ])
                    ->default($defaultData)
                    ->columns(4)
                    ->columnSpanFull()
                    ->grid(1)
                    ->collapsed()
                    ->itemLabel(fn (array $state): ?string => $state['cylinder'] ?? null)
                    ->collapsible()
                    ->label('Table Data')
            ]);
        // Define the default data structure
        $defaultData = [
            [
                'cylinder' => 'Strategy',
                'status' => '',
                'causes' => '',
                'recommendations' => '',
            ],
            [
                'cylinder' => 'Structure',
                'status' => '',
                'causes' => '',
                'recommendations' => '',
            ],
            [
                'cylinder' => 'People Systems',
                'status' => '',
                'causes' => '',
                'recommendations' => '',
            ],
            [
                'cylinder' => 'Methods & Tools',
                'status' => '',
                'causes' => '',
                'recommendations' => '',
            ],
            [
                'cylinder' => 'Lateral Processes',
                'status' => '',
                'causes' => '',
                'recommendations' => '',
            ],
            [
                'cylinder' => 'Metrics',
                'status' => '',
                'causes' => '',
                'recommendations' => '',
            ],
            [
                'cylinder' => 'Cyber',
                'status' => '',
                'causes' => '',
                'recommendations' => '',
            ],
        ];

        return parent::make($name)
            ->schema([
                Repeater::make('rows')
                    ->schema([
                        TextInput::make('cylinder')
                            ->label('Cylinders')
                            ->required(),
                        Select::make('status')
                            ->label('Pain or Positive')
                            ->options([
                                'pain' => 'Pain Point',
                                'positive' => 'Positive',
                            ]),
                        Textarea::make('causes')
                            ->label('Causes')
                            ->rows(2),
                        Textarea::make('recommendations')
                            ->label('Recommendations')
                            ->rows(2),
                    ])
                    ->default($defaultData)
                    ->columns(4)
                    ->columnSpanFull()
                    ->collapsible()
                    ->label('Table Data')
                    ->default($defaultData)
                    ->columns(4)
                    ->columnSpanFull()
                    ->grid(4)
                    ->collapsed()
                    ->itemLabel(fn (array $state): ?string => $state['cylinder'] ?? null)
                    ->collapsible()
                    ->compact()
                    ->disableItemCreation()
                    ->disableItemDeletion()
                    ->disableItemMovement()
                    ->label('Table Data')
            ]);

        $cylinders = [
            'strategy' => 'Strategy',
            'structure' => 'Structure',
            'people_systems' => 'People Systems',
            'methods_tools' => 'Methods & Tools',
            'lateral_processes' => 'Lateral Processes',
            'metrics' => 'Metrics',
            'cyber' => 'Cyber',
        ];

        $schema = [];

        foreach ($cylinders as $key => $label) {
            $schema[] = Grid::make()
                ->schema([
                    Select::make("{$key}.type")
                        ->label('Pain or Positive')
                        ->options([
                            'pain' => 'Pain Point',
                            'positive' => 'Positive',
                        ])
                        ->columnSpan(1),
                    Textarea::make("{$key}.causes")
                        ->label('Causes')
                        ->rows(2)
                        ->columnSpan(1),
                    Textarea::make("{$key}.recommendations")
                        ->label('Recommendations')
                        ->rows(2)
                        ->columnSpan(1),
                ])
                ->columns(3)
                ->columnSpan('full');
        }

        return parent::make($name)
            ->schema([
                Section::make()
                    ->schema($schema)
                    ->columns(1)
                    ->heading('Cylinders Analysis')
                    ->collapsible()
            ]);
        $defaultCylinders = [
            'strategy' => 'Strategy',
            'structure' => 'Structure',
            'people_systems' => 'People Systems',
            'methods_tools' => 'Methods & Tools',
            'lateral_processes' => 'Lateral Processes',
            'metrics' => 'Metrics',
            'cyber' => 'Cyber',
        ];

        // Create default items array with all cylinders
        $defaultItems = collect($defaultCylinders)->map(function ($label, $value) {
            return [
                'cylinder' => $value,
                'type' => 'pain',
                'causes' => '',
                'recommendations' => '',
            ];
        })->values()->toArray();

        return parent::make($name)
            ->schema([
                Repeater::make('rows')
                    ->schema([
                        Select::make('cylinder')
                            ->label('Cylinder')
                            ->options($defaultCylinders)
                            ->required()
                            ->disabled(), // Since we want to maintain the specific order
                        Select::make('type')
                            ->label('Pain or Positive')
                            ->options([
                                'pain' => 'Pain Point',
                                'positive' => 'Positive',
                            ])
                            ->required(),
                        Textarea::make('causes')
                            ->label('Causes')
                            ->rows(2)
                            ->required(),
                        Textarea::make('recommendations')
                            ->label('Recommendations')
                            ->rows(2)
                            ->required(),
                    ])
                    ->columnSpanFull()
                    ->columns([
                        'default' => 1,
                        'sm' => 2,
                        'lg' => 4,
                    ])
                    ->default($defaultItems)
                    ->disableItemCreation() // Prevent adding new rows
                    ->disableItemDeletion() // Prevent deleting rows
                    ->collapsible()
            ]);
    }

}

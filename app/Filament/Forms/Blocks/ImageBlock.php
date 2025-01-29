<?php

namespace App\Filament\Forms\Blocks;

use Filament\Forms\Components\Builder\Block;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;

class ImageBlock extends Block
{
    public static function make(string $name = 'image'): static
    {
        return parent::make('image')
            ->schema([
                SpatieMediaLibraryFileUpload::make('image')
                    ->collection('content_images')
                    ->image()
                    ->imageEditor()
                    ->imageEditorAspectRatios([
                        '16:9',
                        '4:3',
                        '1:1',
                    ])
                    ->downloadable()
                    ->openable()
                    ->required()
                    ->maxSize(5120) // 5MB
                    ->imageCropAspectRatio('16:9')
                    ->imageResizeTargetWidth('1920')
                    ->imageResizeTargetHeight('1080')
                    ->deleteUploadedFileUsing(function ($record) {
                        // This will be called when the block is removed
                        if ($record) {
                            $record->clearMediaCollection('content_images');
                        }
                    })
                    ->columnSpanFull(),
                Select::make('alignment')
                    ->options([
                        'text-left' => 'Left',
                        'text-center' => 'Center',
                        'text-right' => 'Right',
                    ])
                    ->default('text-center')
                    ->required()
                    ->columnSpan([
                        'sm' => 1,
                        'default' => '2'
                    ]),
                Select::make('size')
                    ->options([
                        'sm' => 'Small',
                        'md' => 'Medium',
                        'lg' => 'Large',
                        'full' => 'Full Width',
                    ])
                    ->default('md')
                    ->required()
                    ->columnSpan([
                        'sm' => 1,
                        'default' => '2'
                    ]),
            ])
            ->columns([
//                'sm' => 1,
                'default' => 2,
            ]);
    }

}

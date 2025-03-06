<?php

namespace App\Filament\Forms\Blocks;

    use App\Models\Product;
    use App\Models\Report;
    use Filament\Forms\Components\Builder\Block;
    use Filament\Forms\Components\Group;
    use Filament\Forms\Components\Hidden;
    use Filament\Forms\Components\Radio;
    use Filament\Forms\Components\Section;
    use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
    use Filament\Forms\Components\ViewField;
    use Filament\Forms\Get;
    use Filament\Forms\Set;
    use Illuminate\Database\Eloquent\Model;
    use Rupadana\FilamentSlider\Components\InputSlider;
    use Rupadana\FilamentSlider\Components\InputSliderGroup;

    class ProductBlock extends Block {

        public static function getProducts(Report $record) : array {
            return once(function() use ($record) {
                try {
                    return $record
                        ->survey
                        ->client
                        ->tenant
                        ->products()
                        ->orderBy('name','asc')
                        ->select([
                            'id',
                            'name'
                        ])
                        ->get()
                        ->pluck('name', 'id')
                        ->toArray();
                } catch (\Throwable $e) {
                    return [];
                }
            });
        }

    public static function make(string $name = 'center-logo'): static
    {
        return parent::make($name)
            ->schema([
                Select::make('product_id')
                    ->label(trans('products.singular'))
                    ->options(function(Report $record) {
                        return static::getProducts($record);
                    })
                    ->required()
                    ->reactive()
                    ->afterStateUpdated(function (Get $get, Set $set, $state) {
                        if (!$state) {
                            $set('image_url', false);
                            return;
                        }

                        // Check if the product has an image
                        $product = Product::find($state);
                        $hasImage = $product && $product->hasMedia('logo');

                        if ($hasImage) {
                            $set('image_url', $product->getMedia('logo')->first()->getUrl());
                        } else {
                            $set('image_url', false);
                        }

                        // Set default image width if an image exists
                        if ($hasImage && !$get('image_width')) {
                            $set('image_width', 300);
                        }
                    }),
                Hidden::make('image_url')
                    ->default(false),

                Group::make([
                    Section::make('Image Settings')
                        ->schema([
                            Radio::make('image_size_preset')
                                ->label('Image Size')
                                ->options([
                                    'small' => 'Small (200px)',
                                    'medium' => 'Medium (300px)',
                                    'large' => 'Large (500px)',
                                    'custom' => 'Custom',
                                ])
                                ->default('medium')
                                ->reactive()
                                ->afterStateUpdated(function (Get $get, Set $set, $state) {
                                    if ($state === 'custom') {
                                        return;
                                    }

                                    $widths = [
                                        'small' => 200,
                                        'medium' => 300,
                                        'large' => 500,
                                    ];

                                    $set('image_width', $widths[$state] ?? 300);
                                }),

                            TextInput::make('image_width')
                                ->label('Custom Width (px)')
                                ->numeric()
                                ->minValue(100)
                                ->maxValue(800)
                                ->step(10)
                                ->default(300)
                                ->visible(fn (Get $get): bool => $get('image_size_preset') === 'custom'),

                            ViewField::make('image_preview')
                                ->view('filament.forms.components.product-image-preview')


                        ])
                        ->visible(fn (Get $get): bool => (bool)$get('image_url')),
                ])
            ]);
    }

}

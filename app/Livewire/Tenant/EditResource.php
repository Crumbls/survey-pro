<?php

namespace App\Livewire\Tenant;

use App\Models\Tenant;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Masterix21\Addressable\Models\Address;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class EditResource extends Component implements HasForms
{
    use InteractsWithForms;

    public string $modelId;
    public string $modelType;

    protected Model $record;

    public array $data;

    public array $logo;

    public function mount(string $modelId, string $modelType)
    {
        $this->modelId = $modelId;
        $this->modelType = $modelType;

        $model = $this->getRecord();

        // Initialize form before filling data
        $this->form = $this->form($this->makeForm());

        // Get current data including media
        $this->data = $model->toArray();
//        $this->data['primary_color'] = $this->data['primary_color'] ?? '#f0f0f0';

        // Get current logo if exists
        if (false && $model->hasMedia('logo')) {
            $media = $model->getFirstMedia('logo');
            // Get the relative path that FileUpload component expects
/*
            dd([
                'media' => $media,
                'path' => $media ? $media->getPath() : null,
                'url' => $media ? $media->getUrl() : null,
                'relative_path' => $media ? $media->getPathRelativeToRoot() : null,
            ]);
*/
  //          $this->data['logo'] = $media ? $media->getPathRelativeToRoot() : null;
//            $this->data['logo'] = $media ? $media->getPath() : null;

            $this->data['logo'] = $media ? str_replace('public/', '', $media->getPathRelativeToRoot()) : null;
// For FileUpload, we just need the relative path
            $this->data['logo'] = $media ? $media->getPathRelativeToRoot() : null;

            $this->data['logo'] = $media->getUrl();
            $this->data['logo'] = $media->uuid;
        }

//        dd($this->data);

        $this->form->fill($this->data);
    }

    protected function getRecord(): Model
    {
        if (isset($this->record)) {
            return $this->record;
        }

        $class = $this->modelType;
        $record = $class::find($this->modelId);
        abort_if(!$record, 404);
        $this->record = $record;
        return $this->record;
    }
    public function form(Form $form): Form
    {
        return $form

            ->statePath('data')
            ->model($this->getRecord())
            ->schema([
                Grid::make(1)->schema([
                    Section::make('Basic Information')
                        ->schema([
                            TextInput::make('name')
                                ->required()
                                ->maxLength(255),


                            SpatieMediaLibraryFileUpload::make('logo')
  //                              ->image()
//                                ->imageEditor()
                                ->collection('logo')
                            ->visibility('public')
                            ->disk('public')
                            ->openable()

                                ->afterStateUpdated(function ($state) {
                                    $this->logo = $state;

                                     $this->extractColorsFromLogo($state);
                                }),

                        ]),

                    Section::make('Color Scheme')
                        ->description('Customize your organization\'s colors. Upload a logo to automatically extract a color scheme.')
                        ->schema([
                            Grid::make(3)
                                ->schema([
                                    ColorPicker::make('primary_color')
                                        ->label('Primary Color')
                                        ->required(),

                                    ColorPicker::make('secondary_color')
                                        ->label('Secondary Color')
                                        ->required(),

                                    ColorPicker::make('accent_color')
                                        ->label('Accent Color')
                                        ->required(),
                                ]),
                        ]),
                ]),
            ]);
    }

    protected function extractColorsFromLogo(string $logoPath): void
    {
        return;
        try {
            $fullPath = Storage::disk('public')->path($logoPath);

            if (!file_exists($fullPath)) {
                return;
            }

            $palette = Palette::fromFilename($fullPath);
            $extractor = new ColorExtractor($palette);

            // Extract the 3 most prominent colors
            $colors = $extractor->extract(3);

            if (count($colors) >= 3) {
                $this->form->fill([
                    'primary_color' => $this->rgbToHex($colors[0]),
                    'secondary_color' => $this->rgbToHex($colors[1]),
                    'accent_color' => $this->rgbToHex($colors[2]),
                ]);
            }
        } catch (\Exception $e) {
            // Log the error but don't halt execution
            logger()->error('Color extraction failed', [
                'error' => $e->getMessage(),
                'logo_path' => $logoPath
            ]);
        }
    }

    protected function rgbToHex(int $color): string
    {
        $r = ($color >> 16) & 0xFF;
        $g = ($color >> 8) & 0xFF;
        $b = $color & 0xFF;

        return sprintf("#%02x%02x%02x", $r, $g, $b);
    }

    public function save(): void
    {
        try {
            $data = $this->form->getState();

            $record = $this->getRecord();

            // Handle logo separately if it's a temporary file
            $logo = isset($this->logo) ? $this->logo : null;
            unset($data['logo']);

            // Update the model attributes
            $record->update($data);

            if ($logo) {
                if ($logo instanceof TemporaryUploadedFile) {
                    // Handle TemporaryUploadedFile
                    $record->addMedia($logo->getRealPath())
                        ->usingFileName($logo->getClientOriginalName())
                        ->toMediaCollection('logo');
                } else {
                    $record->clearMediaCollection('logo');

                    if (is_string($logo) && str_starts_with($logo, 'livewire-tmp')) {
                        // Handle temporary uploaded file
                        $tmpPath = storage_path('app/public/' . $logo);
                        if (file_exists($tmpPath)) {
                            $record->addMedia($tmpPath)
                                ->toMediaCollection('logo');
                        }
                    }

                }
            }

            // Log media collection status

            Notification::make()
                ->title('Saved successfully')
                ->success()
                ->send();

            $this->dispatch('tenant-updated');

        } catch (\Exception $e) {
            dd('Save failed:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            Notification::make()
                ->title('Error saving changes')
                ->body($e->getMessage())
                ->danger()
                ->send();

            throw $e;
        }
    }

    public function render()
    {
        return view('livewire.tenant.edit-resource', [
            'record' => $this->getRecord(),
        ]);
    }
}

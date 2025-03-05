<?php

namespace App\Livewire\Client;

use App\Models\Client;
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

    public array $data;

    public $logo;

    public Client $client;

    public function mount()
    {
        // Initialize form before filling data
        $this->form = $this->form($this->makeForm());

        // Get current data including media
        $this->data = $this->client->toArray();

        $this->form->fill($this->data);
    }

    public function form(Form $form): Form
    {
        return $form

            ->statePath('data')
            ->model($this->client)
            ->schema([
                Grid::make(1)->schema([
                    Section::make('Basic Information')
                        ->schema([
                            TextInput::make('name')
                                ->required()
                                ->maxLength(255),
                            SpatieMediaLibraryFileUpload::make('logo')
                                ->collection('logo')
                                ->visibility('public')
                                ->disk('public')
                                ->openable()
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

            $record = $this->client;

            // Handle logo separately if it's a temporary file
            $logo = isset($this->logo) ? $this->logo : null;
            unset($data['logo']);

            // Update the model attributes
            $record->update($data);

            Notification::make()
                ->title('Saved successfully')
                ->success()
                ->send();

            $this->dispatch('client-updated');

        } catch (\Exception $e) {

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
        return view('livewire.client.edit-resource', [
            'record' => $this->client,
            'title' => 'test',
            'subtitle' => 'test2'
        ]);
    }
}

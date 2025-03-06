<?php

namespace App\Livewire\Product;

use App\Filament\Forms\Blocks\CenterLogoBlock;
use App\Filament\Forms\Blocks\ChartsBlock;
use App\Filament\Forms\Blocks\ClientLogoBlock;
use App\Filament\Forms\Blocks\CylindersBlock;
use App\Filament\Forms\Blocks\HeadingBlock;
use App\Filament\Forms\Blocks\HeatmapBlock;
use App\Filament\Forms\Blocks\ImageBlock;
use App\Filament\Forms\Blocks\PistonBlock;
use App\Models\Client;
use App\Models\Product;
use App\Models\Survey;
use App\Models\Tenant;
use App\Traits\HasBreadcrumbs;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;
use Filament\Forms\Components\Builder;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
class EditResource extends Component implements HasForms
{
    use HasBreadcrumbs,
        InteractsWithForms;

    public ?array $data = [];

    public $logo;

    public ?Product $product = null;
    public ?Tenant $tenant = null;

    public function mount(): void
    {
        abort_if(!Gate::allows('update', $this->product), 403);

        $this->data = $this->product->toArray();

        $this->form->fill($this->data);

        $this->tenant = $this->product->tenant;

        $this->addBreadcrumb('Center: '.$this->tenant->name, route('tenants.show', $this->tenant));
        $this->addBreadcrumb('Product: '.$this->product->name, route('products.show', $this->product));
        $this->addBreadcrumb('All Products', route('products.index', $this->product));


    }
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name'),
                Textarea::make('description'),
                SpatieMediaLibraryFileUpload::make('logo')
                                                  ->image()
                                ->imageEditor()
                    ->collection('logo')
                    ->visibility('public')
                    ->disk('public')
                    ->openable()
            ])
            ->statePath('data')
            ->model($this->product);
    }

    public function save(): void
    {
        abort_if(!Gate::allows('update', $this->product), 403);

        $data = $this->form->getState();

        $this->product->update($data);

        // Handle any orphaned media
        $this->cleanupOldMedia();

        Notification::make()
            ->title('Saved successfully')
            ->success()
            ->send();
//        $this->redirectRoute('responses.show', $this->record);
    }

    public function cleanupOldMedia(): void
    {
        return;
        $record = $this->getRecord();
        $content = $this->data ?? [];

        return;

        $usedMediaIds = collect($content)
            ->filter(fn ($block) => $block['type'] === 'image')
            ->pluck('data.image')
            ->filter()
            ->toArray();



        // Delete any media not referenced in the content
        $this->getMedia('content_images')
            ->reject(fn ($media) => in_array($media->id, $usedMediaIds))
            ->each(fn ($media) => $media->delete());
    }

    public function render(): View
    {
        return view('livewire.product.edit-resource', [
            'breadcrumbs' => $this->getBreadcrumbs(),
            'title' => __('products.singular_edit'),
            'subtitle' => __('products.description'),
//            'cancelUrl' => $this->tenant ? route('tenants.clients.index', $this->tenant) : route('clients.index'),
            'saveText' => __('products.singular_update'),
            'record' => $this->product

        ]);
    }
}

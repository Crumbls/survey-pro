<?php

namespace App\Livewire\Report;

use App\Filament\Forms\Blocks\ChartsBlock;
use App\Filament\Forms\Blocks\CylindersBlock;
use App\Filament\Forms\Blocks\HeadingBlock;
use App\Filament\Forms\Blocks\HeatmapBlock;
use App\Filament\Forms\Blocks\ImageBlock;
use App\Models\Report as Model;
use App\Models\Survey;
use App\Traits\HasBreadcrumbs;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Select;
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
    public Model $record;

    public function getRecord(): Model {
        return $this->record;
    }
    public function mount(): void
    {
        abort_if(!Gate::allows('update', $this->record), 403);

        $this->data = $this->record->toArray();

        $this->form->fill($this->data);

        $tenant = $this->record->survey->tenant;

        if ($tenant) {
            $this->addBreadcrumb('Center: '.$tenant->name, route('tenants.show', $tenant));
        } else {
            $this->addBreadcrumb('All Centers', route('surveys.index'));
        }

        if (isset($this->record) && $this->record) {
            $this->addBreadcrumb('Survey: '.$this->record->title, route('surveys.show', $this->record));
        }

    }
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('title'),
                Builder::make('data')
                    ->label('Content')
                    ->blocks([
                        Builder\Block::make('center-logo')
                            ->schema([
                            ])
                            ->columns(1),
                        HeadingBlock::make(''),
                        Builder\Block::make('paragraph')
                            ->schema([
                                Textarea::make('content')
                                    ->label('Paragraph')
                                    ->required(),
                            ]),
                        ImageBlock::make('image'),
                        ChartsBlock::make('chart'),
                        HeatmapBlock::make('heatmap'),
                        CylindersBlock::make('cylinder'),
                    ])
            ])
            ->statePath('data')
            ->model($this->getRecord());
    }

    public function save(): void
    {
        $record = $this->getRecord();
        abort_if(!Gate::allows('update', $record), 403);

        $data = $this->form->getState();

        $this->record->update($data);


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
        return view('livewire.report.edit-resource', [
            'breadcrumbs' => $this->getBreadcrumbs(),
            'record' => $this->getRecord()
        ]);
    }
}

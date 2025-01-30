<?php

namespace App\Livewire\Report;

use App\Filament\Forms\Blocks\CenterLogoBlock;
use App\Filament\Forms\Blocks\ChartsBlock;
use App\Filament\Forms\Blocks\ClientLogoBlock;
use App\Filament\Forms\Blocks\CylindersBlock;
use App\Filament\Forms\Blocks\HeadingBlock;
use App\Filament\Forms\Blocks\HeatmapBlock;
use App\Filament\Forms\Blocks\ImageBlock;
use App\Models\Client;
use App\Models\Collector;
use App\Models\Report;
use App\Models\Report as Model;
use App\Models\Survey;
use App\Models\Tenant;
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

    public ?Report $report = null;
    public ?Survey $survey = null;
    public ?Client $client = null;
    public ?Tenant $tenant = null;

    public function mount(): void
    {
        abort_if(!Gate::allows('update', $this->report), 403);

        $this->data = $this->report->toArray();

        $this->form->fill($this->data);

        $this->survey = $this->report->survey;
        $this->client = $this->survey->client;
        $this->tenant = $this->client->tenant;

        $this->addBreadcrumb('Center: '.$this->tenant->name, route('tenants.show', $this->tenant));
        $this->addBreadcrumb('Client: '.$this->client->name, route('clients.show', $this->client));
        $this->addBreadcrumb('Survey: '.$this->survey->title, route('surveys.show', $this->survey));
        $this->addBreadcrumb('All Reports', route('surveys.reports.index', $this->survey));


        return;
        $tenant = $this->record->survey->tenant;

        if ($tenant) {
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
                        CenterLogoBlock::make('center-logo'),
                        ClientLogoBlock::make('client-logo'),
                        HeadingBlock::make('heading'),
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
            ->model($this->report);
    }

    public function save(): void
    {
        abort_if(!Gate::allows('update', $this->report), 403);

        $data = $this->form->getState();

        $this->report->update($data);


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
        return view('livewire.report.edit-resource', [
            'breadcrumbs' => $this->getBreadcrumbs(),
            'title' => __('reports.singular_edit'),
            'subtitle' => __('reports.description'),
//            'cancelUrl' => $this->tenant ? route('tenants.clients.index', $this->tenant) : route('clients.index'),
            'saveText' => __('reports.singular_update'),
            'record' => $this->report

        ]);
    }
}

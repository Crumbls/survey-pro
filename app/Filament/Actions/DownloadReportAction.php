<?php

namespace App\Filament\Actions;

use Filament\Actions\Action;
use Filament\Support\Enums\Alignment;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\HtmlString;

class DownloadReportAction extends Action
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->label('Download')
            ->icon('heroicon-m-arrow-down-tray')
            ->modalHeading('Preparing Download')
            ->modalContent(fn () => new HtmlString('
                <div class="text-center py-4">
                    <div class="mb-4">
                        <x-filament::loading-indicator class="h-12 w-12 mx-auto" />
                    </div>
                    <p class="text-lg font-medium">Preparing your file for download...</p>
                    <p class="text-sm text-gray-500">Please wait, this may take a moment</p>
                </div>
            '))
            ->modalFooterActions([]) // Remove default buttons
            ->modalAlignment(Alignment::Center)
            ->action(function (Model $record) {
                return;
                // This will be called when the action is triggered
                $this->getLivewire()->dispatch('prepare-download', recordId: $record->id);

                // Keep the modal open
                $this->halt();
            })
            ->extraAttributes([
                'class' => 'hover:bg-primary-500/10',
                'x-on:download-ready.window' => '
                    if ($event.detail.recordId == $el.dataset.recordId) {
                        window.location.href = $event.detail.url;
                        // Close the modal - no need to call unmountAction directly
                        if (typeof $wire.mountedActions !== "undefined") {
                            $wire.mountedActions = {};
                        }
                    }
                '
            ])
            ->color('primary');
    }

    public static function make(?string $name = null): static
    {
        $name = $name ?? 'download';
        return parent::make($name);
    }
}

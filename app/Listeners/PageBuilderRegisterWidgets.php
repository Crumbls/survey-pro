<?php

namespace App\Listeners;

use App\Events\PageBuilderInitialized;
use App\Livewire\Builder\Row;
use App\Livewire\Builder\Section;
use App\Services\PageBuilderRegistry;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class PageBuilderRegisterWidgets
{

    /**
     * Handle the event.
     */
    public function handle(PageBuilderInitialized $event): void
    {
        PageBuilderRegistry::register('section', Section::class);
        PageBuilderRegistry::register('row', Row::class);

        //
    }
}

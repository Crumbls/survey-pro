<?php

namespace App\View\Components\Client;

use App\Models\Client;
use App\Models\Collector;
use App\Models\Survey;
use App\Models\Tenant;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class CollectorCount extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct(public Client $record)
    {
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        $count = $this->record->collectors()->count();

        return view('components.client.collector-count', [
            'url' => route('clients.collectors.index', $this->record),
            'count' => $count,
        ]);
    }
}

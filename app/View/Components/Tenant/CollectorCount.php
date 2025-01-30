<?php

namespace App\View\Components\Tenant;

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
    public function __construct(public Tenant $record)
    {
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        $count = Collector::whereIn('survey_id', Survey::where('tenant_id', $this->record->getKey())->select('surveys.id'))->count();

        return view('components.tenant.collector-count', [
            'url' => route('tenants.collectors.index', $this->record),
            'count' => $count,
        ]);
    }
}

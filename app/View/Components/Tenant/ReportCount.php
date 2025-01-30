<?php

namespace App\View\Components\Tenant;

use App\Models\Client;
use App\Models\Collector;
use App\Models\Report;
use App\Models\Survey;
use App\Models\Tenant;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class ReportCount extends Component
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
        $count = Report::whereIn('client_id', Client::where('tenant_id', $this->record->getKey())->select('clients.id'))->count();

        return view('components.tenant.report-count', [
            'url' => route('tenants.reports.index', $this->record),
            'count' => $count,
        ]);
    }
}

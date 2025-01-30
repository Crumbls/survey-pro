<?php

namespace App\View\Components\Tenant;

use App\Models\Tenant;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class ClientCount extends Component
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
        return view('components.tenant.client-count', [
            'url' => route('tenants.clients.index', $this->record),
            'count' => $this->record->clients()->count(),
        ]);
    }
}

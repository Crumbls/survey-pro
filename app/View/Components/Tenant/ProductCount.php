<?php

namespace App\View\Components\Tenant;

use App\Models\Client;
use App\Models\Survey;
use App\Models\Tenant;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class ProductCount extends Component
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

        $count = $this->record->products()->count();

        return view('components.tenant.product-count', [
            'url' => route('tenants.products.index', $this->record),
            'count' => $count,
        ]);
    }
}

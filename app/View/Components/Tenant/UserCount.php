<?php

namespace App\View\Components\Tenant;

use App\Models\Client;
use App\Models\Survey;
use App\Models\Tenant;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class UserCount extends Component
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

        $count = $this->record->users()->count();

        return view('components.tenant.user-count', [
            'url' => route('tenants.users.index', $this->record),
            'count' => $count,
        ]);
    }
}

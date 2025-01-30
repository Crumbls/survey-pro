<?php

namespace App\View\Components\Client;

use App\Models\Client;
use App\Models\Survey;
use App\Models\Tenant;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class SurveyCount extends Component
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
        $count = $this->record->surveys()->count();

        return view('components.client.survey-count', [
            'url' => route('clients.surveys.index', $this->record),
            'count' => $count,
        ]);
    }
}

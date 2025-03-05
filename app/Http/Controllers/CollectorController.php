<?php

namespace App\Http\Controllers;

use App\Models\Collector;
use App\Models\Survey;
use App\Traits\HasBreadcrumbs;
use Illuminate\Http\Request;

class CollectorController extends Controller
{
    use HasBreadcrumbs;



    /**
     * Display the specified resource.
     */
    public function show(string $uniqueCode)
    {
        $record = Collector::where('unique_code', $uniqueCode)->firstOrFail();

        if ($record->status == 'closed' || !$record->survey) {
            return view('collector.closed', [
                'record' => $record,
                'breadcrumbs' => $this->getBreadcrumbs()
            ]);
        }

        if ($record->survey->questions === null) {
            return view('collector.closed', [
                'record' => $record,
                'breadcrumbs' => $this->getBreadcrumbs()
            ]);
        }


//        dd($record->survey->questions);
        return view('collector.show', [
            'record' => $record,
            'surveyJson' => $record->survey->questions
        ]);
    }

}

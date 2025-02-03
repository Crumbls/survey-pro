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
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {

        dd(__LINE__);
        return view('report.index');
    }


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

        $temp = Survey::inRandomOrder()
            ->where('id', '<>', $record->survey->id)
            ->take(1)
            ->first()
            ->toArray();

        dd($record->survey, $temp);


//        dd($record->survey->questions);
        return view('collector.show', [
            'record' => $record,
            'surveyJson' => $record->survey->questions
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}

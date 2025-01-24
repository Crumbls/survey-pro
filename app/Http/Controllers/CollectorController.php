<?php

namespace App\Http\Controllers;

use App\Models\Collector;
use App\Models\Survey;
use Illuminate\Http\Request;

class CollectorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {

        dd(__LINE__);
        return view('report.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {

        $user = request()->user();

        $surveyId = request()->survey;

        if (!$surveyId) {
            $surveyId = request()->surveyId;
        }

        $surveys = Survey::whereRaw('1=1')
            // Add in active only.
            ->whereIn('tenant_id', $user->tenants()->select('tenants.id'))
            ->when($surveyId, function ($query, $surveyId) {
                return $query->where('uuid', $surveyId)
                    ->take(1);
            })->get();

        abort_if($surveys->isEmpty(), 404);

        $defaultReference = null;

        do {
            $defaultReference = (string)\Str::uuid();
        } while (Collector::where('unique_code', $defaultReference)->take(1)->first());

        return view('collector.create', [
            'surveys' => $surveys,
            'defaultReference' => $defaultReference,
            'defaultGoal' => 100
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        $user = $request->user();
        $tenants = $user->tenants;

        $rules = [
            'reference' => ['required', 'string', 'max:250', 'regex:/^[a-zA-Z0-9\-]+$/', 'unique:collectors,unique_code'],
            'goal' => ['required', 'integer', 'min:1'],
            'survey_id' => ['required', 'numeric']
        ];

        $data = $request->validate($rules);

        $survey = Survey::whereIn('tenant_id', $user->tenants()->select('tenants.id'))
            ->where('surveys.id', $data['survey_id'])
            ->take(1)
            ->firstOrFail();

        $survey->collectors()->create(['name' => '',
            'type' => 'url',
            'status' => 'open',
            'configuration' => [],
            'unique_code' => $data['reference'],
            'expires_at' => null
        ]);

        return redirect()
            ->route('survey.collectors.index', $survey)
            ->with('success', 'Survey target created successfully.');
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $uniqueCode)
    {
        $record = Collector::where('unique_code', $uniqueCode)->firstOrFail();

        if ($record->status == 'closed' || !$record->survey) {
            dd('show closed page.');
        }

//        dd($record->survey->questions);
        return view('collector.show', [
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

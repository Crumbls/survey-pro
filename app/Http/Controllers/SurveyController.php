<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSurveyRequest;
use App\Models\Survey;
use App\Services\TenantService;
use Illuminate\Http\Request;

class SurveyController extends Controller
{
    public function __construct(protected TenantService $tenantService) {

    }
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $user = auth()->user();
        $tenants = $user->tenants;

        // If user only has one tenant, we'll use that by default
        $defaultTenant = $tenants->count() === 1 ? $tenants->first() : null;

        /**
         * TODO: Add in the proper policy
         */
        return view('survey.create', compact('tenants', 'defaultTenant'));
    }

    /**
     * Store a newly created resource in storage.
     */

    public function store(StoreSurveyRequest $request)
    {
        $data = $request->validated();

        $user = $request->user();

        $data['user_id'] = $user->getKey();

        if (!array_key_exists('tenant_id', $data) || !$data['tenant_id']) {
            $tenant = $this->tenantService->getOrCreateDefault($user);
            $data['tenant_id'] = $tenant->getKey();
        }

        $record = Survey::create($data);

        return redirect()
            ->route('surveys.edit', $record)
            ->with('success', 'Survey created successfully. You can now add questions.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, string $record)
    {
        $user = $request->user();

        $record = Survey::where('uuid', $record)
            ->whereIn('tenant_id', $user->tenants()->select('tenants.id'))
            ->take(1)
            ->firstOrFail();

        dd($record);
        dd($record);
        echo 'show the survey landing page.';
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $uuid)
    {
        $user = request()->user();
        $record = Survey::whereRaw('1=1')
            ->where('uuid', $uuid)
            ->whereIn('tenant_id', $user->tenants()->select('tenants.id'))
            ->firstOrFail();

        return view('survey.edit', [
            'record' => $record
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $uuid)
    {
        $data = $request->validate([
            'definition' => [
                'required',
                'json'
            ]
        ]);

        $user = request()->user();

        $record = Survey::whereRaw('1=1')
            ->where('uuid', $uuid)
            ->whereIn('tenant_id', $user->tenants()->select('tenants.id'))
            ->firstOrFail();

        $record->update([
            'questions' => $data['definition']
        ]);

        return response()->json([
            'message' => 'Survey updated successfully',
            'survey' => $record
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}

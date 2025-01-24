<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSurveyRequest;
use App\Models\Survey;
use App\Models\Tenant;
use App\Services\TenantService;
use App\Traits\HasBreadcrumbs;
use Illuminate\Http\Request;

class TenantController extends Controller
{
    use HasBreadcrumbs;

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

        $record = $user->tenants()->where('tenants.uuid', $record)->firstOrFail();

        if ($user->tenants->count() == 1) {
            $this->addBreadcrumb('Centers: '.$record->name);
        } else {
            $this->addBreadcrumb('All Centers', route('tenants.index'));
            $this->addBreadcrumb('Centers: '.$record->name);

        }


        return view('tenant.show', [
            'record' => $record,
            'breadcrumbs' => $this->getBreadcrumbs()
        ]);
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

        $dat = json_decode($data['definition'], true);

        if (array_key_exists('title', $dat) && $dat['title'] && $dat['title'] != $record->title) {
            $record->title = $dat['title'];
        }

        if (array_key_exists('description', $dat) && $dat['description'] && $dat['description'] != $record->description) {
            $record->description = $dat['description'];
        }

        $record->questions = $data['definition'];

        $record->save();

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

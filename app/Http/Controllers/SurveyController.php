<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSurveyRequest;
use App\Models\Survey;
use App\Models\Tenant;
use App\Services\TenantService;
use App\Traits\HasBreadcrumbs;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class SurveyController extends Controller
{
    use HasBreadcrumbs;

    public function __construct(protected TenantService $tenantService) {

    }
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        abort_if(!Gate::allows('create', Survey::class), 403);
        $tenant = null;

        if ($tenantId = request()->tenantId) {
            $tenant = Tenant::where('uuid', $tenantId)->firstOrFail();
            $this->addBreadcrumb(trans('tenants.singular').': '.$tenant->name, route('tenants.show', $tenant));
            $this->addBreadcrumb('Create Survey', route('tenants.surveys.create', $tenant));
        } else {
            $this->addBreadcrumb('All Surveys', route('surveys.index'));
            $this->addBreadcrumb('Create Survey', route('surveys.create'));
        }

        $user = auth()->user();
        $tenants = $tenant ? collect([$tenant]) : $user->tenants;

        // If user only has one tenant, we'll use that by default
        $defaultTenant = $tenants->count() === 1 ? $tenants->first() : null;

        return view('survey.create', [
            'tenants' => $tenants,
            'defaultTenant' => $defaultTenant,
            'breadcrumbs' => $this->getBreadcrumbs()
        ]);
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

        $this->addBreadcrumb(trans('tenants.singular').': '.$record->tenant->name, route('tenants.show', $record->tenant));
        $this->addBreadcrumb('Survey: '.$record->title);

        return view('survey.show', [
            'record' => $record,
            'breadcrumbs' => $this->getBreadcrumbs()
        ]);

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

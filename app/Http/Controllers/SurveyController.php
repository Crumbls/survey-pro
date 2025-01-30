<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSurveyRequest;
use App\Models\Client;
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
     * Display the specified resource.
     */
    public function show(Request $request, string $record)
    {
        $record = request()->survey;

        abort_if(!$record || !$record->getKey(), 404);

        $user = $request->user();

        /**
         * Check to see if the client exists under a tenant the user is a member of.
         */
        abort_if(!Client::whereIn('clients.tenant_id', $user->tenants()->select('tenants.id'))->where('clients.id', $record->client_id)->exists(), 403);

        $this->addBreadcrumb(
            trans('tenants.singular').': '.$record->client->tenant->name,
            route('tenants.show', $record->client->tenant)
        );
        $this->addBreadcrumb(
            trans('clients.singular').': '.$record->client->name,
            route('clients.show', $record->client)
        );
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
            ->whereIn('client_id', Client::whereIn('tenant_id', $user->tenants()->select('tenants.id'))->select('clients.id'))
//            ->whereIn('tenant_id', $user->tenants()->select('tenants.id'))
            ->firstOrFail();
dd($record->client);
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

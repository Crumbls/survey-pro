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

class ClientController extends Controller
{
    use HasBreadcrumbs;

    /**
     * Display the specified resource.
     */
    public function __invoke(Request $request, Client $client)
    {
        $tenant  = $client->tenant;

        abort_if(!$client || !$client->getKey(), 404);

        abort_if(!Gate::allows('update', $client), 403);

        $this->addBreadcrumb(trans('tenants.singular').': '.$tenant->name);
        $this->addBreadcrumb(trans('clients.singular').': '.$client->name);

        return view()->first([
            'clients.'.$client->uuid,
            'clients.edit'
        ], [
            'record' => $client,
            'breadcrumbs' => $this->getBreadcrumbs()
        ]);
    }


}

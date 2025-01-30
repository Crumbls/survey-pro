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

    /**
     * Display the specified resource.
     */
    public function __invoke(Request $request, Tenant $tenant)
    {
        if ($tenant && $tenant->getKey()) {
            $this->addBreadcrumb(trans('tenants.singular').': '.$tenant->name);
        } else {
            $this->addBreadcrumb(trans('tenants.all'), route('tenants.index'));
            $this->addBreadcrumb(trans('tenants.plural').': '.$tenant->name);
        }

        return view()->first([
            'tenants.'.$tenant->uuid,
            'tenant.show'
        ], [
            'record' => $tenant,
            'breadcrumbs' => $this->getBreadcrumbs()
        ]);
    }


}

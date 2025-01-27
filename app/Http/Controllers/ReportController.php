<?php

namespace App\Http\Controllers;

use App\Models\Report;
use App\Models\Survey;
use App\Traits\HasBreadcrumbs;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    use HasBreadcrumbs;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('report.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('report.create');
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     * TODO: Route model binding is not working here.  Figure out why.
     */
    public function show(Request $request, string $record)
    {
        $user = request()->user();

        $record = Report::where('id', $record)
            ->whereIn('survey_id',
                Survey::whereRaw('1=1')
                    ->whereIn('tenant_id',
                        $user->tenants()->select('tenants.id')
                    )
                    ->select('surveys.id')
            )
            ->firstOrFail()
        ;

        $tenant = $record->survey->tenant;

        $this->addBreadcrumb(trans('tenants.singular').': '.$tenant->name, route('tenants.show', $tenant));
        $this->addBreadcrumb('Survey: '.$record->survey->title, route('surveys.show', $record->survey));
        $this->addBreadcrumb('Report: '.$record->title);


        /**
         * TODO: Add in authorization.
         */

        return view('report.show', [
            'tenant' => $tenant,
            'breadcrumbs' => $this->getBreadcrumbs(),
            'record' => $record
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $record)
    {
        $user = request()->user();

        $record = Report::where('id', $record)
            ->whereIn('survey_id',
                Survey::whereRaw('1=1')
                    ->whereIn('tenant_id',
                        $user->tenants()->select('tenants.id')
                    )
                    ->select('surveys.id')
            )
            ->firstOrFail()
        ;

        /**
         * TODO: Add in authorization.
         */

        return view('report.edit', [
            'record' => $record
        ]);
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

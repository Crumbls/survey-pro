<?php

namespace App\Http\Controllers;

use App\Models\Client;
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
    public function show(Request $request, Report $report)
    {
        abort_if(!$report || !$report->getKey(), 404);

        $user = request()->user();


        /**
         * Pull via client, not tenant anymore.
         */
        abort_if(!Report::where($report->getKeyName(), $report->getKey())
            ->whereIn('client_id',
                Client::whereIn('clients.tenant_id',
                    request()->user()->tenants()->select('tenants.id')
                )
                    ->select('clients.id')


            )
            ->take(1)
            ->exists(), 403);

        $this->addBreadcrumb('Center: ' . $report->client->tenant->name, route('tenants.show', $report->client->tenant));
        $this->addBreadcrumb('Client: ' . $report->client->name, route('clients.show', $report->client));
        $this->addBreadcrumb('Survey: ' . $report->survey->title, route('surveys.show', $report->survey));

        return view('report.show', [
            'breadcrumbs' => $this->getBreadcrumbs(),
            'record' => $report,

            'title' => __('reports.singular'),
            'subtitle' => __('reports.description'),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     * @deprecated
     */
    public function edit(string $record)
    {
        $user = request()->user();

        dd(__METHOD__, $record);
        $record = Report::where('id', $record)
            ->whereIn('survey_id',
                Survey::whereRaw('1=1')
                    ->whereIn('tenant_id',
                        $user->tenants()->select('tenants.id')
                    )
                    ->select('surveys.id')
            )
            ->firstOrFail();

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

<?php

namespace App\Http\Controllers;

use App\Models\Survey;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ReportCollectorController extends CollectorController
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        abort(500);
        $record = $request->record;

        abort_if(!Str::of($record)->isUuid(), 404);

        $user = $request->user();

        $record = Survey::where('uuid', $record)
            ->whereIn('tenant_id', $user->tenants()->select('tenants.id'))
            ->where('surveys.uuid', $record)
            ->take(1)
            ->firstOrFail();

        dd($record->collectors);

        dd($record);
        return view('report.index');
    }
}

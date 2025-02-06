<?php

namespace App\Http\Middleware;

use App\Models\Tenant;
use Closure;
use Illuminate\Http\Request;
use Silber\Bouncer\BouncerFacade;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class TenantMemberThroughReport
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        abort_if(!$user, 403);

        abort_if(!$request->report || !$request->report->getKey(), 404);

        $report = $request->report;

        abort_if(!$report->survey, 404);

        abort_if(!$report->survey->tenant, 404);

        $tenant = $report->survey->tenant;

        abort_if(!$tenant->users()->where('user_id', $user->getKey())->exists(), 403);

        return $next($request);
    }
}

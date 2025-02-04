<?php

namespace App\Http\Middleware;

use App\Models\Tenant;
use Closure;
use Illuminate\Http\Request;
use Silber\Bouncer\BouncerFacade;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class TenantMember
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        abort_if(!$user, 403);

        $tenant = $request->route('tenant');

        abort_if(!$tenant || !$tenant->getKey(), 404);

        abort_if(!$tenant->users()->where('user_id', $user->getKey())->exists(), 403);

        return $next($request);
    }
}

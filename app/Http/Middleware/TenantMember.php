<?php

namespace App\Http\Middleware;

use App\Models\Tenant;
use Closure;
use Illuminate\Http\Request;
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

        if (!$tenant->users()->where('user_id', $user->getKey())->exists()) {
            /**
             * Log access attempt.
             */
            abort(403);
        }

        // Add tenant to the request for easy access in your controllers/components
        $request->attributes->add(['tenant' => $tenant]);

        return $next($request);
    }
}

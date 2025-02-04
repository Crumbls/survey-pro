<?php

namespace App\Http\Middleware;

use Closure;
use Silber\Bouncer\Bouncer;
use Silber\Bouncer\BouncerFacade;

class ScopeBouncer
{
    /**
     * The Bouncer instance.
     *
     * @var \Silber\Bouncer\Bouncer
     */
    protected $bouncer;

    /**
     * Constructor.
     */
    public function __construct(Bouncer $bouncer)
    {
        $this->bouncer = $bouncer;
    }

    /**
     * Set the proper Bouncer scope for the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $user = $request->user();

        abort_if(!$user, 403);

        $tenant = $request->route('tenant');

        if ($tenant) {
            // Add tenant to the request for easy access in your controllers/components
            $request->attributes->add(['tenant' => $tenant]);
            $this->bouncer->scope()->to($tenant->getKey())->onlyRelations(false)->dontScopeRoleAbilities();
        }



        return $next($request);
    }
}

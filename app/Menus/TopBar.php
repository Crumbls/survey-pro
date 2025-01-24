<?php

namespace App\Menus;

use App\Models\Role;
use App\Models\Tenant;
use Illuminate\Support\Facades\Gate;
use Spatie\Menu\Laravel\Menu;
use Spatie\Menu\Laravel\Link;
use Illuminate\Support\Facades\Auth;

class TopBar
{
    protected static array $childPatterns = [
        'dashboard' => ['dashboard*'],
        'surveys' => ['surveys*', 'responses*'],
        'reports' => ['reports*', 'export*', 'downloads*'],
        'analytics' => ['analytics*', 'metrics*', 'insights*']
    ];

    public static function render(): Menu
    {
        $menu = Menu::new()
            ->wrap('nav', ['class' => 'hidden md:block'])
            ->setWrapperTag('ul')
            ->addClass('flex items-center gap-6')
            ->addItemParentClass('list-none');

        if (Auth::guest()) {
            static::addGuestLinks($menu);
        } else {
            static::addAuthenticatedLinks($menu);
        }

        return $menu
            ->setActiveClass('text-primary-600 underline underline-offset-8')
            ->addItemClass('text-sm font-medium text-slate-600 hover:text-primary-600 hover:underline hover:underline-offset-8')
            ->setActiveFromRequest()
            /*
            ->setActive(function ($item) {
                if (!$item->hasUrl()) {
                    return false;
                }

                $segment = trim($item->url(), '/');
                $baseSegment = explode('/', $segment)[0];

                // Check exact match
                if (request()->is($segment)) {
                    return true;
                }

                // Check child patterns
                if (isset(static::$childPatterns[$baseSegment])) {
                    foreach (static::$childPatterns[$baseSegment] as $pattern) {
                        if (request()->is($pattern)) {
                            return true;
                        }
                    }
                }

                return false;
            })
            */
            ;
    }

    protected static function addGuestLinks(Menu $menu): void
    {
        $menu->add(Link::to('/', 'Home'))
            ->add(Link::to('/login', 'Login'))
            ->add(Link::to('/register', 'Register'));
    }

    protected static function addAuthenticatedLinks(Menu $menu): void
    {
        $user = Auth::user();
        $tenantCount = $user->tenants()->count();
        $tenant = $tenantCount === 1 ? $user->tenants->first() : null;

        if ($tenantCount == 1) {
            // Dashboard - Available to all authenticated users
            $menu->add(Link::toRoute('tenants.show', 'Dashboard', ['tenant' => $tenant]));
        } else {
            // Dashboard - Available to all authenticated users
            $menu->add(Link::toRoute('dashboard', 'Dashboard'));
        }

        // Surveys - Check policy
        if ($tenantCount > 1 && Auth::user()->can('viewAny', \App\Models\Tenant::class)) {
            $menu->add(Link::toRoute('tenants.index', 'Centers'));
        } elseif (Gate::allows('viewAny', Tenant::class)) {
            $menu->add(Link::toRoute('tenants.index', 'Centers'));
        }


        // Surveys - Check policy
        if (Auth::user()->can('viewAny', \App\Models\Survey::class)) {
            $menu->add(Link::toRoute('surveys.index', 'Surveys'));
        }

        // Reports - Check policy and role
        if (Auth::user()->can('viewAny', \App\Models\Report::class)) {
            $menu->add(Link::toRoute('reports.index', 'Reports'));

        }

        // Analytics - Only for users with specific permission
        if (Auth::user()->can('view-analytics')) {
            $menu->add(Link::to('/analytics', 'Analytics'));
        }

    }
}

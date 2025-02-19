<?php

namespace App\Menus;

use App\Models\Client;
use App\Models\Collector;
use App\Models\Report;
use App\Models\Role;
use App\Models\Survey;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Silber\Bouncer\BouncerFacade;
use Silber\Bouncer\BouncerFacade as Bouncer;
use Spatie\Menu\Laravel\Menu;
use Spatie\Menu\Laravel\Link;
use Illuminate\Support\Facades\Auth;

class TopBar
{
    protected static array $childPatterns = [
        'dashboard' => ['dashboard*'],
        'clients' => ['clients*'],
        'surveys' => ['surveys*', 'responses*'],
        'collectors' => ['collectors*'],
        'reports' => ['reports*', 'export*', 'downloads*'],
        'analytics' => ['analytics*', 'metrics*', 'insights*']
    ];

    protected static function getRelative(string $input) : string {
        return once(function() use ($input) {
            $base = url('/');
            if (str_starts_with($input, $base)) {
                $input = trim(substr($input, strlen($base)), '/');
            }
            return $input;
        });
    }

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

        $activeUrl = request()->url();

        return $menu
            ->setActiveClass('text-primary-600 underline underline-offset-8')
            ->addItemClass('text-sm font-medium text-slate-600 hover:text-primary-600 hover:underline hover:underline-offset-8')
//            ->setActiveFromUrl($activeUrl)
            ->setActive(function(Link $link) use ($activeUrl) {
                if (!$link->hasUrl()) {
                    return false;
                }

                $url = $link->url();

                if ($url == $activeUrl) {
                    return true;
                }

                /**
                 * Convert activeUrl to relative url.
                 */
                $activeUrl = static::getRelative($activeUrl);
                $url = static::getRelative($url);

                $section = null;

                $uuid = '[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}';

                /**
                 * Simplify it.
                 */
                if (preg_match('#^(surveys|reports|analytics)\/#', $activeUrl, $temp)) {
                    return strpos($url, $temp[1]) !== false;
                    //if (preg_match('#'.$temp[1].'#'))
                    dd($temp);
                }
                if (preg_match('#^tenants\/' . $uuid . '\/(\w+s)\/?#', $activeUrl, $temp)) {//
                    return strpos($url, $temp[1]) !== false;
                    return false;
                    dd($temp, $url);
                }

                return false;
            })

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
            $menu->route('tenants.show', trans('dashboards.singular'), ['tenant' => $tenant]);
        }

        // Surveys - Check policy
        if ($tenantCount > 1 && Gate::allows('viewAny', Tenant::class)) {
            $menu->add(Link::toRoute('tenants.index', trans('tenants.plural')));
        }

        if ($tenantCount && Gate::allows('viewAny', Client::class)) {
            if ($tenantCount == 1) {
                $menu->add(Link::toRoute('tenants.clients.index', trans('clients.plural'), ['tenant' => $tenant]));
            } else {
                $menu->add(Link::toRoute('clients.index', trans('clients.plural')));
            }
        }

        if (false && $user->can('viewAny', \App\Models\Client::class)) {
            if ($tenantCount) {
                $menu->add(Link::toRoute('tenants.clients.index', trans('clients.plural'), ['tenant' => $tenant]));
            } else if ($tenant) {

                $menu->add(Link::toRoute('clients.index', trans('clients.plural')));
            }
        }

        if (Gate::allows('viewAny', Survey::class)) {
            if ($tenantCount == 1) {
                $menu->add(Link::toRoute('tenants.surveys.index', trans('surveys.plural'), ['tenant' => $tenant]));
            } else {
                $menu->add(Link::toRoute('surveys.index', trans('surveys.plural')));
            }
        }

        if ($tenantCount && Gate::allows('viewAny', Collector::class)) {
            if ($tenantCount == 1) {
                $menu->add(Link::toRoute('tenants.collectors.index', trans('collectors.plural'), ['tenant' => $tenant]));
            } else {
                $menu->add(Link::toRoute('collectors.index', trans('collectors.plural')));
            }
        }


        // Reports - Check policy and role
        if ($tenantCount && Gate::allows('viewAny', Report::class)) {
            if ($tenantCount == 1) {
                $menu->add(Link::toRoute('tenants.reports.index', trans('reports.plural'), ['tenant' => $tenant]));
            } else {
                $menu->add(Link::toRoute('reports.index', trans('reports.plural')));
            }
        }

        if ($tenantCount && Gate::allows('viewAny', User::class)) {
            if ($tenantCount == 1) {
                $menu->add(Link::toRoute('tenants.users.index', trans('users.plural'), ['tenant' => $tenant]));
            } else {
                $menu->add(Link::toRoute('users.index', trans('users.plural')));
            }
        }

        // Analytics - Only for users with specific permission
        if (Gate::allows('view-analytics')) {
            $menu->add(Link::to('/analytics', 'Analytics'));
        }



    }
}

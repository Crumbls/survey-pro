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
                dd($activeUrl, $url);

//                $base = url('/');
                if (preg_match('#^(\w+s)\/?#', $activeUrl, $temp)) {
                    if ($temp[1] == 'tenants') {
                        if (preg_match('#^tenants\/' . $uuid . '/(\w+s)\/?$#', $activeUrl, $temp)) {
                            $section = $temp[1];
                        } else {
                            dd($activeUrl);
                        }
                    } else {
                        dd($temp);
                    }
                } else {
                    dd($activeUrl, $url);
                    dd(__LINE__);
                }

                if ($section) {
                    if (str_starts_with($url, $section)) {
                        return true;
                        dd(__LINE__);
                    }
//                    dd($section);
                }
                dd($activeUrl, $section, $url);
                dd(__LINE__);
                dd($base);


                /**
                 * Extract urls based under /tenants
                 */
                if (preg_match('#\/tenants\/' . $uuid . '\/(\w+s)\/?#', $activeUrl, $matches)) {
                    dd($activeUrl, url($matches[1]));
                    return str_starts_with($activeUrl, url($matches[1]));
                    echo url($matches[1]);
                    dd($matches, $url);
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
        if ($tenantCount > 1) {
            $menu->add(Link::toRoute('tenants.index', trans('tenants.plural')));
        }

        if ($tenantCount && $user->can('viewAny', \App\Models\Survey::class)) {
            if ($tenantCount == 1) {
                $menu->add(Link::toRoute('tenants.surveys.index', trans('surveys.plural'), ['tenantId' => $tenant]));
            } else {
                $menu->add(Link::toRoute('surveys.index', trans('surveys.plural')));
            }
        }

        // Reports - Check policy and role
        if (!$tenantCount) {
        } elseif ($user->can('viewAny', \App\Models\Report::class)) {
            if ($tenantCount == 1) {
                $menu->add(Link::toRoute('tenants.reports.index', trans('reports.plural'), ['tenantId' => $tenant]));
            } else {
                $menu->add(Link::toRoute('reports.index', trans('reports.plural')));
            }
        }

        // Analytics - Only for users with specific permission
        if (Auth::user()->can('view-analytics')) {
            $menu->add(Link::to('/analytics', 'Analytics'));
        }

    }
}

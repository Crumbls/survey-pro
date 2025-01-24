<?php

namespace App\Menus;

use Spatie\Menu\Laravel\Menu;
use Spatie\Menu\Laravel\Link;
use Spatie\Menu\Html;

class MobileTopBar
{
    public static function render(): Menu
    {
        $menu = Menu::new()
            ->wrap('nav', ['class' => 'md:hidden'])
            ->addClass('space-y-1');

        // Add the navigation links
        $menu->add(Link::to('/dashboard', 'Dashboard')
            ->addClass('block px-4 py-2 text-base font-medium text-primary-600 hover:text-primary-900 hover:bg-primary-50'))
            ->add(Link::to('/surveys', 'Surveys')
                ->addClass('block px-4 py-2 text-base font-medium text-primary-600 hover:text-primary-900 hover:bg-primary-50'))
            ->add(Link::to('/reports', 'Reports')
                ->addClass('block px-4 py-2 text-base font-medium text-primary-600 hover:text-primary-900 hover:bg-primary-50'))
            ->add(Link::to('/analytics', 'Analytics')
                ->addClass('block px-4 py-2 text-base font-medium text-primary-600 hover:text-primary-900 hover:bg-primary-50'))
            ->add(Link::to('/profile', 'Profile')
                ->addClass('block px-4 py-2 text-base font-medium text-primary-600 hover:text-primary-900 hover:bg-primary-50'))
            ->add(Link::to('/settings', 'Settings')
                ->addClass('block px-4 py-2 text-base font-medium text-primary-600 hover:text-primary-900 hover:bg-primary-50'));

        // Add the divider
        $menu->add(Html::raw('<div class="border-t border-slate-200 my-2"></div>'));

        // Add the logout form
        $menu->add(Html::raw(static::logoutForm()));

        return $menu;
    }

    protected static function logoutForm(): string
    {
        return sprintf(
            '<form method="POST" action="%s" class="w-full">
                %s
                <button type="submit" class="flex w-full items-center gap-2 px-4 py-2 text-base font-medium text-red-600 hover:text-red-900 hover:bg-red-50">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-5 h-5">
                        <path fill-rule="evenodd" d="M3 4.25A2.25 2.25 0 0 1 5.25 2h5.5A2.25 2.25 0 0 1 13 4.25v2a.75.75 0 0 1-1.5 0v-2a.75.75 0 0 0-.75-.75h-5.5a.75.75 0 0 0-.75.75v11.5c0 .414.336.75.75.75h5.5a.75.75 0 0 0 .75-.75v-2a.75.75 0 0 1 1.5 0v2A2.25 2.25 0 0 1 10.75 18h-5.5A2.25 2.25 0 0 1 3 15.75V4.25Z" />
                        <path fill-rule="evenodd" d="M19 10a.75.75 0 0 0-.75-.75H8.704l1.048-.943a.75.75 0 1 0-1.004-1.114l-2.5 2.25a.75.75 0 0 0 0 1.114l2.5 2.25a.75.75 0 1 0 1.004-1.114l-1.048-.943h9.546A.75.75 0 0 0 19 10Z" />
                    </svg>
                    Sign out
                </button>
            </form>',
            url('/logout'),
            csrf_field()
        );
    }
}

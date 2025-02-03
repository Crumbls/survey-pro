<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;
use Filament\Facades\Filament;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        //
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        Gate::before(function ($user, $ability) {
            return true;
            $panel = Filament::getCurrentPanel();
            $path = $panel?->getPath();
            if (!$path) {
                return null;
            }
            if (request()->is($path.'/*') || request()->is('filament/*')) {
                return auth()->user()->canAccessPanel(Filament::getCurrentPanel($panel));
            }
            return null;
        });
        //
    }
}

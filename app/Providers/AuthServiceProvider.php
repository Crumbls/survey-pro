<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;
use App\Models\User;
use App\Policies\IssuePolicy;
use Crumbls\Issue\Models\Issue;
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


        Gate::define('view-analytics', function() {
            return false;
        });
        /**
         * TODO: Start moving to proper procedures.
         */
        return;
        Gate::before(function (?User $user, string $ability, array $atts = []) {
            if ($ability == 'view-analytics') {
                return false;
            }
            if ($atts) {
                return true;
            }
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

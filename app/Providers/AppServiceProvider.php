<?php

namespace App\Providers;

use App\Components\ButtonComponent;
use App\Services\ComponentRegistry;
use Illuminate\Cache\ArrayStore;
use Illuminate\Contracts\Auth\Access\Gate;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Validator;
use Livewire\Livewire;
use Silber\Bouncer\BouncerFacade as Bouncer;
use Silber\Bouncer\CachedClipboard;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {



        $this->app->singleton(ComponentRegistry::class);
return;
        $this->app->singleton(\Silber\Bouncer\Bouncer::class, function ($app) {
            return Bouncer::make()
                ->withClipboard(new CachedClipboard(new ArrayStore))
                ->withGate($app->make(Gate::class))
                ->create();
        });

        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(ComponentRegistry $componentRegistry): void
    {


        $button = new ButtonComponent();
        $componentRegistry->register('custom-button', $button->toArray());

        Validator::extend('contains_number', function ($attribute, $value, $parameters, $validator) {
            return preg_match('/[0-9]/', $value);
        });

        Validator::extend('contains_special_character', function ($attribute, $value, $parameters, $validator) {
            return preg_match('/[^A-Za-z0-9]/', $value);
        });

    }
}

<?php

namespace App\Providers;

use App\Events\PageBuilderInitialized;
use App\Listeners\PageBuilderRegisterWidgets;
use App\Models\Report;
use App\Models\Tenant;
use App\Models\User;
use App\Observers\TenantObserver;
use App\Services\BizXReportService;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
//            SendEmailVerificationNotification::class,
        ],
        PageBuilderInitialized::class => [
            PageBuilderRegisterWidgets::class
        ]
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        Report::creating(function ($record) {
            BizXReportService::createDefault($record);
        });


        Tenant::observe(TenantObserver::class);
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}

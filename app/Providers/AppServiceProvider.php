<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Event;
use SocialiteProviders\Manager\SocialiteWasCalled;
use SocialiteProviders\Microsoft\MicrosoftExtendSocialite;
use App\Observers\AuditObserver;
use App\Models\AllowedDomain;
use App\Models\Locacion;
use App\Models\Ticket;
use App\Models\TicketAction;
use App\Models\TicketAssignment;
use App\Models\TicketPart;
use App\Models\TicketResolution;
use App\Models\TicketSchedule;
use App\Models\TicketStatusEvent;
use App\Models\User;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Event::listen(
            SocialiteWasCalled::class,
            MicrosoftExtendSocialite::class
        );

        Ticket::observe(AuditObserver::class);
        TicketAssignment::observe(AuditObserver::class);
        TicketAction::observe(AuditObserver::class);
        TicketPart::observe(AuditObserver::class);
        TicketResolution::observe(AuditObserver::class);
        TicketStatusEvent::observe(AuditObserver::class);
        TicketSchedule::observe(AuditObserver::class);
        User::observe(AuditObserver::class);
        Locacion::observe(AuditObserver::class);
        AllowedDomain::observe(AuditObserver::class);
    }
}

<?php

namespace App\Providers;

use App\Models\Competition;
use App\Models\Competitor;
use App\Models\Day;
use App\Models\Payment;
use App\Models\Spectator;
use App\Observers\CompetitionObserver;
use App\Observers\CompetitorObserver;
use App\Observers\DayObserver;
use App\Observers\PaymentObserver;
use App\Observers\SpectatorObserver;
use App\Services\Wca\Wca;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(Wca::class, fn ($app) => new Wca());
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Http::macro('wca', function () {
            return Http::baseUrl('https://www.worldcubeassociation.org/api/v0');
        });
        Competition::observe(CompetitionObserver::class);
        Competitor::observe(CompetitorObserver::class);
        Day::observe(DayObserver::class);
        Payment::observe(PaymentObserver::class);
        Spectator::observe(SpectatorObserver::class);
    }
}

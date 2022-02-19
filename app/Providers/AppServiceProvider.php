<?php

namespace App\Providers;

use App\Models\Competition;
use App\Models\Competitor;
use App\Models\Day;
use App\Observers\CompetitionObserver;
use App\Observers\CompetitorObserver;
use App\Observers\DayObserver;
use App\Services\Wca\Wca;
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
        Competition::observe(CompetitionObserver::class);
        Competitor::observe(CompetitorObserver::class);
        Day::observe(DayObserver::class);
    }
}

<?php

namespace App\Providers;

use App\Models\Competition;
use App\Models\Day;
use App\OAuth\WcaProvider;
use App\Observers\CompetitionObserver;
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
        Day::observe(DayObserver::class);
    }
}

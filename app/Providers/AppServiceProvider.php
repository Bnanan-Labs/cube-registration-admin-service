<?php

namespace App\Providers;

use App\Models\Competition;
use App\Models\Day;
use App\OAuth\WcaProvider;
use App\Observers\CompetitionObserver;
use App\Observers\DayObserver;
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
        //
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

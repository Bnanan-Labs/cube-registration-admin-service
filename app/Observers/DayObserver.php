<?php

namespace App\Observers;

use App\Models\Day;

class DayObserver
{
    /**
     * Handle the Day "creating" event.
     *
     * @param  \App\Models\Day  $day
     * @return void
     */
    public function creating(Day $day): void
    {
        $day->week_day = $day->date->weekday();
        $day->competition_id = 1;
    }

    public function created(Day $day): void
    {
        Day::all()
            ->sortBy('date')
            ->each(fn (Day $day, $i) => $day->update(['sort_id' => $i]));
    }
}

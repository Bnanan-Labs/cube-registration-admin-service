<?php

namespace App\Observers;

use App\Models\Competition;
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
        if (!$day->isDirty('competition_id')) {
            $day->competition()->associate(Competition::first());
        }
    }

    public function created(Day $day): void
    {
        Day::where('competition_id', $day->competition_id)
            ->get()
            ->sortBy('date')
            ->each(fn (Day $day, $i) => $day->update(['sort_id' => $i]));
    }
}

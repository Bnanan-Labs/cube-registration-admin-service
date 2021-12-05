<?php

namespace App\Observers;

use App\Models\Competition;
use App\Models\Day;
use App\Models\FinancialBook;
use Carbon\CarbonPeriod;

class CompetitionObserver
{
    public function creating(Competition $competition): void
    {
        $competition->finances()->associate(FinancialBook::create());
    }

    public function created(Competition $competition): void
    {
        $period = CarbonPeriod::create($competition->start_date, $competition->end_date);
        foreach ($period as $key => $date) {
            if ($key > 5) { return; }
            $dayNumber = $key + 1;
            Day::create([
                'title' => "Day {$dayNumber}",
                'week_day' => $date->weekday(),
                'sort_id' => $key,
                'date' => $date,
                'is_bookable' => true,
                'competition_id' => $competition->id,
            ]);
        }
    }
}

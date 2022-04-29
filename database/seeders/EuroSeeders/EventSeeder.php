<?php

namespace Database\Seeders\EuroSeeders;

use App\Models\Competition;
use App\Models\Event;
use App\Services\Finances\MoneyBag;
use App\Services\Wca\Enums\Event as EventEnum;
use App\Services\Wca\Wca;
use Illuminate\Database\Seeder;

class EventSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $events = collect([
            ['222', 3500, null, 10_000, 60_000],
            ['333', 3500, null, null, 120_000],
            ['444', 3500, 60_000, null, 120_000],
            ['555', 3500, 110_000, null, 180_000],
            ['666', 3500, 210_000, null, 300_000],
            ['777', 3500, 285_000, null, 360_000],
            ['3oh', 3500, 30_000, null, 60_000],
            ['3bld', 3500, 180_000, null, 300_000],
            ['4bld', 7500, null, null, 900_000],
            ['5bld', 7500, null, null, 1_800_000],
            ['fmc', 7500, null, null, 3_600_000],
            ['mbld', 7500, null, null, 3_600_000],
            ['pyram', 3500, null, 10_000, 60_000],
            ['minx', 3500, 120_000, null, 180_000],
            ['sq1', 3500, 25_000, null, 60_000],
            ['clock', 3500, 12_000, null, 60_000],
            ['skewb', 3500, null, 10_000, 60_000],
        ]);

        $euro = Competition::first();
        $wcaService = new Wca();
        $events->each(function (array $event) use ($euro, $wcaService) {
            [$wcaId, $price, $qualification, $cutoff, $timeLimit] = $event;
            Event::create([
                'competition_id' => $euro->id,
                'wca_event_id' => $wcaId,
                'title' => $wcaService->event(EventEnum::from($wcaId))->fullName,
                'fee' => new MoneyBag($price, $euro->currency),
                'qualification_limit' => $qualification,
                'cutoff_limit' => $cutoff,
                'time_limit' => $timeLimit,
            ]);
        });
    }
}

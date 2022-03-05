<?php

namespace Database\Seeders\EuroSeeders;

use App\Models\Competition;
use App\Models\Event;
use App\Services\Wca\Enums\Event as EventEnum;
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
            '222',
            '333',
            '444',
            '555',
            '666',
            '777',
            '3oh',
            '3bld',
            '4bld',
            '5bld',
            'fmc',
            'pyram',
            'minx',
            'sq1',
            'clock',
            'skewb',
        ]);

        $euro = Competition::first();
        $events->each(Fn (string $event) => Event::factory()->create([
            'competition_id' => $euro->id,
            'wca_event_id' => $event,
            'title' => EventEnum::from($event),
        ]));
    }
}

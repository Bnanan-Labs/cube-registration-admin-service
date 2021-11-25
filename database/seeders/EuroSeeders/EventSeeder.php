<?php

namespace Database\Seeders\EuroSeeders;

use App\Models\Competition;
use App\Models\Event;
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
            '2x2x2',
            '3x3x3',
            '4x4x4',
            '5x5x5',
            '6x6x6',
            '7x7x7',
            '3x3x3 One-handed',
            '3x3x3 Blindfolded',
            '4x4x4 Blindfolded',
            '5x5x5 Blindfolded',
            '3x3x3 Fewest moves',
            'Pyraminx',
            'Megaminx',
            'Square-1',
            'Clock',
            'Skewb',
        ]);

        $euro = Competition::first();
        $events->each(Fn (string $event) => Event::factory()->create(['competition_id' => $euro->id, 'title' => $event]));
    }
}

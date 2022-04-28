<?php

namespace Database\Seeders\EuroSeeders;

use App\Models\Competition;
use App\Services\Finances\MoneyBag;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class CompetitionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Competition::factory()->create([
            'title' => 'Rubik\'s WCA European Championships 2022',
            'start_date' => Carbon::create(2022, 7,14),
            'end_date' => Carbon::create(2022, 7,17),
            'is_active' => true,
            'registration_starts' => Carbon::create(2022, 4, 29, 20),
            'registration_ends' => Carbon::create(2022, 6, 10),
            'volunteer_registration_starts' => Carbon::create(2022, 2),
            'volunteer_registration_ends' => Carbon::create(2022, 7),
            'base_fee' => new MoneyBag(amount: 40000),
            'guest_fee' => new MoneyBag(amount: 20000),
            'currency' => 'DKK',
            'competitor_limit' => 1200,
            'spectator_limit' => 3000,
        ]);
    }
}

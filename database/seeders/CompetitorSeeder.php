<?php

namespace Database\Seeders;

use App\Models\Competitor;
use Illuminate\Database\Seeder;

class CompetitorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Competitor::factory()->create(5);
    }
}

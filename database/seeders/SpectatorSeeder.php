<?php

namespace Database\Seeders;

use App\Models\Spectator;
use Illuminate\Database\Seeder;

class SpectatorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Spectator::factory()->create(5);
    }
}

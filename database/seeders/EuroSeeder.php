<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class EuroSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            \Database\Seeders\EuroSeeders\CompetitionSeeder::class,
            \Database\Seeders\EuroSeeders\EventSeeder::class,
            \Database\Seeders\EuroSeeders\StaffRoleSeeder::class,
            CompetitorSeeder::class,
        ]);
    }
}

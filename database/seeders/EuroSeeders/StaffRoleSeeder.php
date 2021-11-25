<?php

namespace Database\Seeders\EuroSeeders;

use App\Models\StaffRole;
use Illuminate\Database\Seeder;

class StaffRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $roles = collect([
            'Scrambling',
            'Judging',
            'Cleaning',
            'Live Streaming',
            'Check-in',
            'Running',
            'ScoreKeeping',
        ]);

        $roles->each(Fn (string $role) => StaffRole::factory()->create(['title' => $role]));
    }
}

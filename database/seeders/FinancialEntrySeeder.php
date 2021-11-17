<?php

namespace Database\Seeders;

use App\Models\FinancialEntry;
use Illuminate\Database\Seeder;

class FinancialEntrySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        FinancialEntry::factory()->create(5);
    }
}

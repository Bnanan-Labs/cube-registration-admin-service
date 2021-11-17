<?php

namespace Database\Seeders;

use App\Models\FinancialBook;
use Illuminate\Database\Seeder;

class FinancialBookSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        FinancialBook::factory()->create(5);
    }
}

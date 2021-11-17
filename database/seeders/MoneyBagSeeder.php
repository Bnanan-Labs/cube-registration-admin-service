<?php

namespace Database\Seeders;

use App\Models\MoneyBag;
use Illuminate\Database\Seeder;

class MoneyBagSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        MoneyBag::factory()->create(5);
    }
}

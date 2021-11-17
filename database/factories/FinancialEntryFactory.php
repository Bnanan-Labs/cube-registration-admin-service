<?php

namespace Database\Factories;

use App\Models\FinancialEntry;
use Illuminate\Database\Eloquent\Factories\Factory;

class FinancialEntryFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = FinancialEntry::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
           'type' => $this->faker->word(),
           'title' => $this->faker->sentence(2),
           'balance' => $this->faker->numberBetween(0,100),
           'booked_at' => $this->faker->datetime(),

        ];
    }
}

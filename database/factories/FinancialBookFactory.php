<?php

namespace Database\Factories;

use App\Models\FinancialBook;
use Illuminate\Database\Eloquent\Factories\Factory;

class FinancialBookFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = FinancialBook::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
           'balance' => $this->faker->numberBetween(0,100),
           'paid' => $this->faker->numberBetween(0,100),

        ];
    }
}

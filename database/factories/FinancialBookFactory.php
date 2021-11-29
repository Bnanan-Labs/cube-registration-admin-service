<?php

namespace Database\Factories;

use App\Models\FinancialBook;
use App\Services\Finances\MoneyBag;
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
            'balance' => new MoneyBag(amount: $this->faker->numberBetween(0,100)),
            'paid' => new MoneyBag(amount: $this->faker->numberBetween(0,100)),
            'total' => new MoneyBag(amount: $this->faker->numberBetween(0,100)),

        ];
    }
}

<?php

namespace Database\Factories;

use App\Models\MoneyBag;
use Illuminate\Database\Eloquent\Factories\Factory;

class MoneyBagFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = MoneyBag::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
           'currency' => $this->faker->word(),
           'total' => $this->faker->randomNumber(),

        ];
    }
}

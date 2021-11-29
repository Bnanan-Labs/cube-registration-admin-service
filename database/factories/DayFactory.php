<?php

namespace Database\Factories;

use App\Models\Day;
use App\Services\Finances\MoneyBag;
use Illuminate\Database\Eloquent\Factories\Factory;

class DayFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Day::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence(2),
            'week_day' => $this->faker->dayOfWeek(),
            'sort_id' => $this->faker->randomNumber(),
            'date' => $this->faker->date(),
            'price' => new MoneyBag(amount: $this->faker->numberBetween(0,100)),
            'is_bookable' => $this->faker->boolean(),
            'competition_id' => $this->faker->numberBetween(0, 100),
        ];
    }
}

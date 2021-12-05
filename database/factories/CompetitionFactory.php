<?php

namespace Database\Factories;

use App\Models\Competition;
use App\Models\FinancialBook;
use App\Services\Finances\MoneyBag;
use Illuminate\Database\Eloquent\Factories\Factory;

class CompetitionFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Competition::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'title' => $this->faker->sentence(2),
            'start_date' => $this->faker->date(),
            'end_date' => $this->faker->date(),
            'is_active' => $this->faker->boolean(),
            'registration_starts' => $this->faker->datetime(),
            'registration_ends' => $this->faker->datetime(),
            'volunteer_registration_starts' => $this->faker->datetime(),
            'volunteer_registration_ends' => $this->faker->datetime(),
            'base_fee' => new MoneyBag(amount: $this->faker->numberBetween(1,100)),
            'competitor_limit' => $this->faker->randomNumber(),
            'spectator_limit' => $this->faker->randomNumber(),
        ];
    }
}

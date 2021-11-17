<?php

namespace Database\Factories;

use App\Models\Spectator;
use Illuminate\Database\Eloquent\Factories\Factory;

class SpectatorFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Spectator::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
           'first_name' => $this->faker->sentence(2),
           'last_name' => $this->faker->sentence(2),
           'email' => $this->faker->sentence(2),
           'registration_status' => $this->faker->word(),
           'payment_status' => $this->faker->word(),

        ];
    }
}

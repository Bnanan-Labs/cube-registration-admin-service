<?php

namespace Database\Factories;

use App\Models\Competitor;
use Illuminate\Database\Eloquent\Factories\Factory;

class CompetitorFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Competitor::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        return [
           'first_name' => $this->faker->sentence(2),
           'last_name' => $this->faker->sentence(2),
           'wca_id' => $this->faker->word(),
           'email' => $this->faker->email(),
           'registration_status' => $this->faker->word(),
           'payment_status' => $this->faker->word(),
           'has_podium_potential' => $this->faker->boolean(),
           'nationality' => $this->faker->sentence(2),
           'is_eligible_for_prizes' => $this->faker->boolean(),
            'financial_book_id' => 1,
        ];
    }
}

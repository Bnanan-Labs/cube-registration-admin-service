<?php

namespace Database\Factories;

use App\Models\Competition;
use App\Models\Competitor;
use App\Models\FinancialBook;
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
            'first_name' => $this->faker->firstName(),
            'last_name' => $this->faker->lastName(),
            'wca_id' => $this->faker->regexify('20[0-2][0-9][A-Z]{4}[0-9]{2}'),
            'email' => $this->faker->email(),
            'gender' => $this->faker->randomLetter(),
            'registration_status' => $this->faker->word(),
            'payment_status' => $this->faker->word(),
            'has_podium_potential' => $this->faker->boolean(),
            'nationality' => $this->faker->countryCode(),
            'is_eligible_for_prizes' => $this->faker->boolean(),
            'financial_book_id' => FinancialBook::factory()->create()->id,
            'competition_id' => Competition::factory()->create()->id,
        ];
    }
}

<?php

namespace Database\Factories;

use App\Enums\PaymentStatus;
use App\Enums\RegistrationStatus;
use App\Enums\Wca;
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
            'wca_id' => $this->faker->regexify(Wca::idRegex->value),
            'email' => $this->faker->email(),
            'gender' => $this->faker->randomLetter(),
            'registration_status' => $this->faker->randomElement(RegistrationStatus::cases()),
            'payment_status' => $this->faker->randomElement(PaymentStatus::cases()),
            'has_podium_potential' => $this->faker->boolean(),
            'nationality' => $this->faker->countryCode(),
            'is_eligible_for_prizes' => $this->faker->boolean(),
            'financial_book_id' => FinancialBook::factory()->create()->id,
            'competition_id' => Competition::factory()->create()->id,
        ];
    }

    /**
     * Indicates that the user has a manager role
     *
     * @param $competitionId
     * @return CompetitorFactory
     */
    public function competition($competitionId): CompetitorFactory
    {
        return $this->state(Fn (array $attributes): array => ['competition_id' => $competitionId]);
    }
}

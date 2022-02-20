<?php

namespace Database\Factories;

use App\Enums\RegistrationStatus;
use App\Enums\ShirtSize;
use App\Enums\Wca;
use App\Models\Competition;
use App\Models\Staff;
use Illuminate\Database\Eloquent\Factories\Factory;

class StaffFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Staff::class;

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
            'wca_id' => $this->faker->regexify(Wca::idRegex->value),
            'application' => $this->faker->sentence(2),
            'registration_status' => $this->faker->randomElement(RegistrationStatus::cases()),
            't_shirt_size' => $this->faker->randomElement(ShirtSize::cases()),
            'competition_id' => Competition::factory()->create()->id,
        ];
    }

    /**
     * Indicates that the user has a manager role
     *
     * @param $competitionId
     * @return StaffFactory
     */
    public function competition($competitionId): StaffFactory
    {
        return $this->state(Fn (array $attributes): array => ['competition_id' => $competitionId]);
    }
}

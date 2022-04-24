<?php

namespace Database\Factories;

use App\Models\Payment;
use Illuminate\Database\Eloquent\Factories\Factory;

class PaymentFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Payment::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'intent_id' => $this->faker->uuid(),
            'intent_secret' => $this->faker->uuid(),
            'total' => $this->faker->randomNumber(),
            'extra' => $this->faker->sentence(2),

        ];
    }
}

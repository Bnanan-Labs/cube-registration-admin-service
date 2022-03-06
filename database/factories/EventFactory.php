<?php

namespace Database\Factories;

use App\Models\Event;
use App\Services\Wca\Enums\Event as WcaEvent;
use App\Services\Finances\MoneyBag;
use Illuminate\Database\Eloquent\Factories\Factory;

class EventFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Event::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'title' => $this->faker->sentence(2),
            'wca_event_id' => $this->faker->randomElement(WcaEvent::cases())->value,
            'qualification_limit' => $this->faker->numberBetween(0,100),
            'cutoff_limit' => $this->faker->numberBetween(0,100),
            'competitor_limit' => $this->faker->numberBetween(100, 300),
            'time_limit' => $this->faker->numberBetween(0, 100),
            'competition_id' => $this->faker->numberBetween(0, 100),
            'fee' => new MoneyBag(amount: $this->faker->numberBetween(0,100)),
        ];
    }
}

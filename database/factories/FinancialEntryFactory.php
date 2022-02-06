<?php

namespace Database\Factories;

use App\Enums\FinancialEntryType;
use App\Models\FinancialEntry;
use App\Services\Finances\MoneyBag;
use Illuminate\Database\Eloquent\Factories\Factory;

class FinancialEntryFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = FinancialEntry::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'type' => $this->faker->randomElement(FinancialEntryType::cases()),
            'title' => $this->faker->sentence(2),
            'balance' => new MoneyBag($this->faker->numberBetween(0,100)),
            'booked_at' => $this->faker->datetime(),
            'financial_book_id' => $this->faker->numberBetween(0,100),
        ];
    }
}

<?php

namespace Database\Factories;

use App\Enums\Wca;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        $name = $this->faker->name();
        $wcaId = $this->faker->regexify(Wca::idRegex->value);
        $nationality = $this->faker->countryCode();
        $email = $this->faker->unique()->safeEmail();
        return [
            'name' => $name,
            'email' => $email,
            'wca_id' => $wcaId,
            'nationality' => $nationality,
            'email_verified_at' => now(),
            'raw' => json_encode([
                    'class' => 'user',
                    'url' => 'https =>\/\/www.worldcubeassociation.org\/persons\/' . $wcaId,
                    'id' => 6256,
                    'wca_id' => $wcaId,
                    'name' => $name,
                    'gender' => 'm',
                    'country_iso2' => $nationality,
                    'delegate_status' => null,
                    'created_at' => '2015-10-27T07:22:24.000Z',
                    'updated_at' => '2021-11-27T11:27:00.000Z',
                    'teams' => [],
                    'avatar' => [
                        'url' => 'https =>\/\/www.worldcubeassociation.org\/assets\/missing_avatar_thumb-12654dd6f1aa6d458e80d02d6eed8b1fbea050a04beeb88047cb805a4bfe8ee0.png',
                        'thumb_url' => 'https =>\/\/www.worldcubeassociation.org\/assets\/missing_avatar_thumb-12654dd6f1aa6d458e80d02d6eed8b1fbea050a04beeb88047cb805a4bfe8ee0.png',
                        'is_default' => true,
                    ],
                    'email' => $email,
            ]),
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * Indicates that the user has a manager role
     *
     * @return UserFactory
     */
    public function manager(): UserFactory
    {
        return $this->state(Fn (array $attributes): array => ['is_manager' => true]);
    }

    /**
     * Indicate that the model's email address should be unverified.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function unverified(): Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'email_verified_at' => null,
            ];
        });
    }
}

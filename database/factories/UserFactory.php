<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $name = $this->faker->name();
        $wcaId = $this->faker->regexify('20[0-2][0-9][A-Z]{4}[0-9]{2}');
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
     * Indicate that the model's email address should be unverified.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function unverified()
    {
        return $this->state(function (array $attributes) {
            return [
                'email_verified_at' => null,
            ];
        });
    }
}

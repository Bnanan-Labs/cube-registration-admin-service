<?php

namespace Tests\Feature\GraphQL;

use App\Jobs\RegisterCompetitor;
use App\Models\Competitor;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\GraphQLTestCase;

class RegistrationTest extends GraphQLTestCase
{
    use RefreshDatabase, WithFaker;

    /**
     * A basic test example.
     *
     * @return void
     */
    public function testCanRegister()
    {
        /** @var User $user */
        $user = User::factory()->create();
        $this->authenticate($user);
        Queue::fake();
        Queue::assertNothingPushed();
        $registration = [
            'first_name' => $this->faker->firstName(),
            'last_name' => $this->faker->lastName(),
            'email' => $this->faker->email(),
            'guests' => [$this->faker->name(), $this->faker->name()],
            'is_interested_in_nations_cup' => $this->faker->boolean(),
            'events' => [$this->faker->numberBetween(1,10), $this->faker->numberBetween(1,10)],
            'days' => [$this->faker->numberBetween(1,10), $this->faker->numberBetween(1,10)],
        ];

        $this->graphQL(/** @lang GraphQL */ '
            mutation ($input: RegisterCompetitorInput!)
            {
                registerCompetitor(input: $input) {
                    id
                }
            }
        ', [
            'input' => $registration
        ])->assertJSON([
            'data' => [
                'registerCompetitor' => [
                    'id' => 1234
                ]
            ]
        ]);

        Queue::assertPushed(Fn (RegisterCompetitor $job) => $job->registration['email'] === $registration['email']);
        Queue::assertPushed(Fn (RegisterCompetitor $job) => $job->user['wca_id'] === $user->wca_id);
    }

    /**
     * A basic test example.
     *
     * @return void
     */
    public function testCanRejectRegistration()
    {
        /** @var User $user */
        $user = User::factory()->create(['wca_id' => '2010TEST01']);
        $this->authenticate($user);
        Competitor::factory()->create(['wca_id' => '2010TEST01']);
        Queue::fake();
        Queue::assertNothingPushed();
        $registration = [
            'first_name' => $this->faker->firstName(),
            'last_name' => $this->faker->lastName(),
            'email' => $this->faker->email(),
            'guests' => [$this->faker->name(), $this->faker->name()],
            'is_interested_in_nations_cup' => $this->faker->boolean(),
            'events' => [$this->faker->numberBetween(1,10), $this->faker->numberBetween(1,10)],
            'days' => [$this->faker->numberBetween(1,10), $this->faker->numberBetween(1,10)],
        ];

        $this->graphQL(/** @lang GraphQL */ '
            mutation ($input: RegisterCompetitorInput!)
            {
                registerCompetitor(input: $input) {
                    id
                }
            }
        ', [
            'input' => $registration
        ])->assertJSON([
            'errors' => [
                [
                    'message' => 'You already have a pending registration',
                ],
            ],
        ]);

        Queue::assertNothingPushed();
    }
}

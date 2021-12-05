<?php

namespace Tests\Feature\GraphQL;

use App\Models\Day;
use App\Models\Spectator;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\GraphQLTestCase;

class SpectatorTest extends GraphQLTestCase
{
    use RefreshDatabase, WithFaker;

    public function testCanViewAllFields()
    {
        /** @var User $user */
        $user = User::factory()->create(['is_manager' => true]);
        $this->authenticate($user);
        $spectator = Spectator::factory()->create();

        $this->graphQL(/** @lang GraphQL */ '
            query ($id: ID!) {
                spectator(id: $id) {
                    id
                    first_name
                    last_name
                    email
                    payment_status
                    registration_status
                }
            }
        ', [
            'id' => $spectator->id,
        ])->assertJSON([
            'data' => [
                'spectator' => [
                    'first_name' => $spectator->first_name,
                    'last_name' => $spectator->last_name,
                    'email' => $spectator->email,
                    'registration_status' => $spectator->registration_status,
                    'payment_status' => $spectator->payment_status,
                ],
            ],
        ]);
    }

    public function testCanCreateEndpoint()
    {
        /** @var User $user */
        $user = User::factory()->create(['is_manager' => true]);
        $this->authenticate($user);
        $day = Day::factory()->create();

        $input = [
            'first_name' => $this->faker()->firstName(),
            'last_name' => $this->faker()->lastName(),
            'email' => $this->faker()->email(),
            'days' => [
                'sync' => [$day->id],
            ],
        ];

        $this->graphQL(/** @lang GraphQL */ '
            mutation ($input: CreateSpectatorInput){
                createSpectator(input: $input) {
                    first_name
                    last_name
                    email
                    days {
                        id
                    }
                }
            }
        ', [
            'input' => $input,
        ])->assertJSON([
            'data' => [
                'createSpectator' => [
                    'first_name' => $input['first_name'],
                    'last_name' => $input['last_name'],
                    'email' => $input['email'],
                    'days' => [[
                        'id' => $day->id,
                    ]],
                ],
            ],
        ]);
    }

    public function testCanUpdateEndpoint()
    {
        /** @var User $user */
        $user = User::factory()->create(['is_manager' => true]);
        $this->authenticate($user);
        $spectator = Spectator::factory()->create();
        $day = Day::factory()->create();

        $input = [
            'id' => $spectator->id,
            'first_name' => $this->faker()->firstName(),
            'last_name' => $this->faker()->lastName(),
            'email' => $this->faker()->email(),
            'days' => [
                'sync' => [$day->id],
            ],
        ];

        $this->graphQL(/** @lang GraphQL */ '
            mutation ($input: UpdateSpectatorInput){
                updateSpectator(input: $input) {
                    first_name
                    last_name
                    email
                    days {
                        id
                    }
                }
            }
        ', [
            'input' => $input,
        ])->assertJSON([
            'data' => [
                'updateSpectator' => [
                    'first_name' => $input['first_name'],
                    'last_name' => $input['last_name'],
                    'email' => $input['email'],
                    'days' => [[
                        'id' => $day->id,
                    ]],
                ],
            ],
        ]);
    }

    public function testCanDeleteEndpoint()
    {
        /** @var User $user */
        $user = User::factory()->create(['is_manager' => true]);
        $this->authenticate($user);
        $spectator = Spectator::factory()->create();

        $this->assertModelExists($spectator);

        $this->graphQL(/** @lang GraphQL */ '
            mutation ($id: ID!){
                deleteSpectator(id: $id) {
                    id
                }
            }
        ', [
            'id' => $spectator->id,
        ])->assertJSON([
            'data' => [
                'deleteSpectator' => [
                    'id' => $spectator->id,
                ],
            ],
        ]);

        $this->assertModelMissing($spectator);
    }

    public function testCanGuardEmailField()
    {
        /** @var User $user */
        $user = User::factory()->create(['is_manager' => false]);
        $spectator = Spectator::factory()->create();

        $query = /** @lang GraphQL */ '
            query ($id: ID!) {
                spectator(id: $id) {
                    email
                }
            }
        ';

        $this->graphQL($query, ['id' => $spectator->id])
            ->assertJSON(self::UNAUTHENTICATED_RESPONSE);

        $this->authenticate($user);
        $this->graphQL($query, ['id' => $spectator->id])
            ->assertJSON(self::UNAUTHORIZED_RESPONSE);
    }

    public function testCanGuardPaymentStatusField()
    {
        /** @var User $user */
        $user = User::factory()->create(['is_manager' => false]);
        $spectator = Spectator::factory()->create();

        $query = /** @lang GraphQL */ '
            query ($id: ID!) {
                spectator(id: $id) {
                    payment_status
                }
            }
        ';

        $this->graphQL($query, ['id' => $spectator->id])
            ->assertJSON(self::UNAUTHENTICATED_RESPONSE);

        $this->authenticate($user);
        $this->graphQL($query, ['id' => $spectator->id])
            ->assertJSON(self::UNAUTHORIZED_RESPONSE);
    }
}

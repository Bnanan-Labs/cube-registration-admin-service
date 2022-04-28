<?php

namespace Tests\Feature\GraphQL;

use App\Enums\RegistrationStatus;
use App\Enums\Wca;
use App\Jobs\CreateCompetitorBook;
use App\Jobs\RegisterCompetitor;
use App\Models\Competition;
use App\Models\Competitor;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Queue;
use Tests\GraphQLTestCase;

class CompetitorTest extends GraphQLTestCase
{
    use RefreshDatabase, WithFaker;

    public function testCanQueryACompetitor(): void
    {
        /** @var User $user */
        $competitor = Competitor::factory()->create();

        $this->graphQL(/** @lang GraphQL */ '
            query ($id: ID!){
                competitor(id: $id) {
                    id
                    first_name
                    last_name
                    wca_id
                    registration_status
                    has_podium_potential
                    nationality
                    is_eligible_for_prizes
                }
            }
        ', [
            'id' => $competitor->id
        ])->assertJSON([
            'data' => [
                'competitor' => [
                    'id' => $competitor->id,
                    'first_name' => $competitor->first_name,
                    'last_name' => $competitor->last_name,
                    'wca_id' => $competitor->wca_id,
                    'registration_status' => $competitor->registration_status->value,
                    'has_podium_potential' => $competitor->has_podium_potential,
                    'nationality' => $competitor->nationality,
                    'is_eligible_for_prizes' => $competitor->is_eligible_for_prizes,
                ]
            ]
        ]);
    }

    public function testCanQueryACompetitorByWcaId(): void
    {
        $this->authenticate(User::factory()->manager()->create());
        $competitor = Competitor::factory()->create();

        $this->graphQL(/** @lang GraphQL */ '
            query ($wcaId: WcaId, $competitionId: ID){
                competitors(first: 1, wca_id: $wcaId, competition_id: $competitionId) {
                    data {
                        id
                        first_name
                        last_name
                        wca_id
                        registration_status
                        has_podium_potential
                        nationality
                        is_eligible_for_prizes

                    }
                }
            }
        ', [
            'wcaId' => $competitor->wca_id,
            'competitionId' => $competitor->competition_id,
        ])->assertJSON([
            'data' => [
                'competitors' => [
                    'data' => [[
                        'id' => $competitor->id,
                        'first_name' => $competitor->first_name,
                        'last_name' => $competitor->last_name,
                        'wca_id' => $competitor->wca_id,
                        'registration_status' => $competitor->registration_status->value,
                        'has_podium_potential' => $competitor->has_podium_potential,
                        'nationality' => $competitor->nationality,
                        'is_eligible_for_prizes' => $competitor->is_eligible_for_prizes,
                    ]],
                ],
            ],
        ]);
    }

    public function testCanFilterCompetitorsByRegistrationStatus(): void
    {
        $this->authenticate(User::factory()->manager()->create());
        $competitorPending = Competitor::factory()->create(['registration_status' => RegistrationStatus::pending]);
        $competitorApproved = Competitor::factory()->create(['registration_status' => RegistrationStatus::approved]);
        $competitorWaitingList = Competitor::factory()->create(['registration_status' => RegistrationStatus::waitingList]);

        $this->graphQL(/** @lang GraphQL */ '
            query {
                competitors(first: 10 registration_status: APPROVED) {
                    data {
                        id

                    }
                }
            }
        ', [])->assertJSON([
            'data' => [
                'competitors' => [
                    'data' => [[
                        'id' => $competitorApproved->id,
                    ]],
                ],
            ],
        ]);
    }

    public function testCanQueryACompetitorWithSensitiveData(): void
    {
        /** @var User $user */
        $user = User::factory()->manager()->create();
        $this->authenticate($user);
        $competitor = Competitor::factory()->create(['guests' => ['a', 'b', 'c']]);

        $this->graphQL(/** @lang GraphQL */ '
            query ($id: ID!){
                competitor(id: $id) {
                    id
                    first_name
                    last_name
                    wca_id
                    email
                    guests
                    number_of_guests
                    registration_status
                    payment_status
                    has_podium_potential
                    nationality
                    is_eligible_for_prizes
                }
            }
        ', [
            'id' => $competitor->id
        ])->assertJSON([
            'data' => [
                'competitor' => [
                    'id' => $competitor->id,
                    'first_name' => $competitor->first_name,
                    'last_name' => $competitor->last_name,
                    'wca_id' => $competitor->wca_id,
                    'email' => $competitor->email,
                    'guests' => $competitor->guests->toArray(),
                    'number_of_guests' => $competitor->numberOfGuests,
                    'registration_status' => $competitor->registration_status->value,
                    'payment_status' => $competitor->payment_status->value,
                    'has_podium_potential' => $competitor->has_podium_potential,
                    'nationality' => $competitor->nationality,
                    'is_eligible_for_prizes' => $competitor->is_eligible_for_prizes,
                ]
            ]
        ]);
    }

    public function testCanGuardCompetitorsSensitiveFields(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        $competitor = Competitor::factory()->create();

        $query = /** @lang GraphQL */ '
            query ($id: ID!){
                competitor(id: $id) {
                    id
                    first_name
                    last_name
                    wca_id
                    email
                    registration_status
                    payment_status
                    has_podium_potential
                    nationality
                    is_eligible_for_prizes
                }
            }
        ';

        $this->graphQL($query, ['id' => $competitor->id])
            ->assertJSON(self::UNAUTHENTICATED_RESPONSE);

        $this->authenticate($user);
        $this->graphQL($query, ['id' => $competitor->id])
            ->assertJSON(self::UNAUTHORIZED_RESPONSE);
    }

    public function testCanGuardCreateCompetitorMutation(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        Competition::factory()->create();

        $input = [
            'first_name' => 'test',
            'last_name' => 'test',
            'gender' => 'm',
            'guests' => ['guest', 'guest'],
            'is_interested_in_nations_cup' => false,
            'email' => $this->faker->email(),
            'wca_id' => $this->faker->regexify(Wca::idRegex->value),
            'nationality' => 'DENMARK',
        ];

        $query = /** @lang GraphQL */ '
            mutation createCompetitor ($input: CreateCompetitorInput!){
                createCompetitor(input: $input) {
                    id
                    wca_id
                }
            }
        ';

        $this->graphQL($query, ['input' => $input])
            ->assertJSON(self::UNAUTHENTICATED_RESPONSE);

        $this->authenticate($user);
        $this->graphQL($query, ['input' => $input])
            ->assertJSON(self::UNAUTHORIZED_RESPONSE);
    }

    public function testCanCreateCompetitorMutation(): void
    {
        /** @var User $user */
        $user = User::factory()->manager()->create();
        Competition::factory()->create();

        $input = [
            'first_name' => 'test',
            'last_name' => 'test',
            'gender' => 'm',
            'guests' => ['guest', 'guest'],
            'is_interested_in_nations_cup' => false,
            'email' => $this->faker->email(),
            'wca_id' => $this->faker->regexify(Wca::idRegex->value),
            'nationality' => 'DENMARK',
        ];

        $query = /** @lang GraphQL */ '
            mutation createCompetitor ($input: CreateCompetitorInput!){
                createCompetitor(input: $input) {
                    first_name
                }
            }
        ';

        $this->authenticate($user);
        $this->graphQL($query, ['input' => $input])
            ->assertJSON([
                'data' => [
                    'createCompetitor' => [
                        'first_name' => 'test',
                    ],
                ],
            ]);
    }

    public function testCanUpdateCompetitorMutation(): void
    {
        /** @var User $user */
        $user = User::factory()->manager()->create();
        $competitor = Competitor::factory()->create();


        $input = [
            'id' => $competitor->id,
            'first_name' => 'test',
            'last_name' => 'test',
            'gender' => 'f',
            'email' => $this->faker->email(),
            'nationality' => 'DENMARK',
        ];

        $query = /** @lang GraphQL */ '
            mutation updateCompetitor ($input: UpdateCompetitorInput!){
                updateCompetitor(input: $input) {
                    id
                    first_name
                    last_name
                    gender
                    email
                    nationality
                }
            }
        ';

        $this->authenticate($user);
        $this->graphQL($query, ['input' => $input])
            ->assertJSON([
                'data' => [
                    'updateCompetitor' => [
                        'id' => $input['id'],
                        'first_name' => $input['first_name'],
                        'last_name' => $input['last_name'],
                        'gender' => $input['gender'],
                        'email' => $input['email'],
                        'nationality' => $input['nationality'],
                    ],
                ],
            ]);
    }

    public function testCanUpdateRegistrationMutation(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        $competitor = Competitor::factory()->create(['wca_id' => $user->wca_id]);

        $input = [
            'id' => $competitor->id,
            'first_name' => 'test',
            'last_name' => 'test',
            'gender' => 'f',
            'email' => $this->faker->email(),
            'nationality' => 'DENMARK',
        ];

        $query = /** @lang GraphQL */ '
            mutation updateRegistration ($input: UpdateRegistrationInput!){
                updateRegistration(input: $input) {
                    id
                    first_name
                    last_name
                    gender
                    email
                    nationality
                }
            }
        ';

        $this->authenticate($user);
        $this->graphQL($query, ['input' => $input])
            ->assertJSON([
                'data' => [
                    'updateRegistration' => [
                        'id' => $input['id'],
                        'first_name' => $input['first_name'],
                        'last_name' => $input['last_name'],
                        'gender' => $input['gender'],
                        'email' => $input['email'],
                        'nationality' => $input['nationality'],
                    ],
                ],
            ]);
    }
}

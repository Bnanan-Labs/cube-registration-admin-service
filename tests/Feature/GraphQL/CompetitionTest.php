<?php

namespace Tests\Feature\GraphQL;

use App\Models\Competition;
use App\Models\Competitor;
use App\Models\Day;
use App\Models\Event;
use App\Models\Spectator;
use App\Models\Staff;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\GraphQLTestCase;

class CompetitionTest extends GraphQLTestCase
{
    use RefreshDatabase, WithFaker;

    public function testCanQueryACompetition(): void
    {
        /** @var User $user */
        $user = User::factory()->create(['is_manager' => true]);
        $this->authenticate($user);
        $competition = Competition::factory()->create();

        $this->graphQL(/** @lang GraphQL */ '
            query ($id: ID!){
                competition(id: $id) {
                    id
                    title
                    start_date
                    end_date
                    is_active
                    competitor_limit
                    spectator_limit
                }
            }
        ', [
            'id' => $competition->id
        ])->assertJSON([
            'data' => [
                'competition' => [
                    'id' => $competition->id,
                    'title' => $competition->title,
                    'start_date' => $competition->start_date,
                    'end_date' => $competition->end_date,
                    'is_active' => $competition->is_active,
                    'competitor_limit' => $competition->competitor_limit,
                    'spectator_limit' => $competition->spectator_limit,
                ]
            ]
        ]);
    }

    public function testCanUpdateACompetition(): void
    {
        /** @var User $manager */
        $manager = User::factory()->create(['is_manager' => true]);
        $this->authenticate($manager);

        $competition = Competition::factory()->create();
        $currency = 'DKK';
        $competitionInput = [
            'id' => $competition->id,
            'title' => 'Random Competition 2099',
            'start_date' => now()->addMonths(2)->toDateString(),
            'end_date' => now()->addMonths(2)->adddays(3)->toDateString(),
            'registration_starts' => now()->addMonth()->setMilliseconds(0)->toISOString(),
            'registration_ends' => now()->addMonths(2)->setMilliseconds(0)->subDays(7)->toISOString(),
            'volunteer_registration_starts' => now()->setMilliseconds(0)->toISOString(),
            'volunteer_registration_ends' => now()->addMonth()->setMilliseconds(0)->addDays(7)->toISOString(),
            'base_fee' => [
                'currency' => $currency,
                'amount' => 37,
            ],
            'guest_fee' => [
                'currency' => $currency,
                'amount' => 42,
            ],
            'currency' => $currency,
            'competitor_limit' => 100,
            'spectator_limit' => 200,
        ];

        $this->graphQL(/** @lang GraphQL */ '
            mutation ($input: UpdateCompetitionInput){
                updateCompetition(input: $input) {
                    id
                    title
                    start_date
                    end_date
                    base_fee {
                        currency
                        amount
                    }
                    guest_fee {
                        currency
                        amount
                    }
                    currency
                    registration_starts
                    registration_ends
                    volunteer_registration_starts
                    volunteer_registration_ends
                    is_active
                    competitor_limit
                    spectator_limit
                }
            }
        ', [
            'input' => $competitionInput,
        ])->assertJSON([
            'data' => [
                'updateCompetition' => [
                    'id' => $competition->id,
                    'title' => $competitionInput['title'],
                    'base_fee' => $competitionInput['base_fee'],
                    'guest_fee' => $competitionInput['guest_fee'],
                    'currency' => $competitionInput['currency'],
                    'start_date' => $competitionInput['start_date'],
                    'end_date' => $competitionInput['end_date'],
                    'registration_starts' => $competitionInput['registration_starts'],
                    'registration_ends' => $competitionInput['registration_ends'],
                    'volunteer_registration_starts' => $competitionInput['volunteer_registration_starts'],
                    'volunteer_registration_ends' => $competitionInput['volunteer_registration_ends'],
                    'competitor_limit' => $competitionInput['competitor_limit'],
                    'spectator_limit' => $competitionInput['spectator_limit'],
                ]
            ]
        ]);
    }

    public function testManagersCanUpdateCompetition(): void
    {
        /** @var User $manager */
        $manager = User::factory()->create(['is_manager' => true]);
        $this->authenticate($manager);

        $competition = Competition::factory()->create();
        $competitionInput = [
            'id' => $competition->id,
            'title' => 'Random Competition 2099',
        ];

        $this->graphQL(/** @lang GraphQL */ '
            mutation ($input: UpdateCompetitionInput){
                updateCompetition(input: $input) {
                    title
                }
            }
        ', [
            'input' => $competitionInput,
        ])->assertJSON([
            'data' => [
                'updateCompetition' => [
                    'title' => $competitionInput['title'],
                ]
            ]
        ]);
    }

    public function testNonManagersCannotUpdateCompetition(): void
    {
        /** @var User $nonManager */
        $nonManager = User::factory()->create(['is_manager' => false]);
        $this->authenticate($nonManager);

        $competition = Competition::factory()->create();
        $competitionInput = [
            'id' => $competition->id,
            'title' => 'Random Competition 2099',
        ];

        $this->graphQL(/** @lang GraphQL */ '
            mutation ($input: UpdateCompetitionInput){
                updateCompetition(input: $input) {
                    title
                }
            }
        ', [
            'input' => $competitionInput,
        ])->assertJSON(self::UNAUTHORIZED_RESPONSE);
    }

    public function testUpdateCompetitionIsGuarded(): void
    {
        $competition = Competition::factory()->create();
        $competitionInput = [
            'id' => $competition->id,
            'title' => 'Random Competition 2099',
        ];

        $this->graphQL(/** @lang GraphQL */ '
            mutation ($input: UpdateCompetitionInput){
                updateCompetition(input: $input) {
                    title
                }
            }
        ', [
            'input' => $competitionInput,
        ])->assertJSON(self::UNAUTHENTICATED_RESPONSE);
    }

    public function testFinancesAreGuarded(): void
    {
        /** @var User $manager */
        $manager = User::factory()->create(['is_manager' => true]);
        $competition = Competition::factory()->create();

        $query = /** @lang GraphQL */ '
            query ($id: ID!){
                competition(id: $id) {
                    finances {
                        balance {
                            amount
                        }
                    }
                }
            }
        ';

        /* Unauthenticated */
        $this->graphQL( $query, ['id' => $competition->id])
            ->assertJSON(self::UNAUTHENTICATED_RESPONSE);

        /* Authorised */
        $this->authenticate($manager);
        $this->graphQL( $query, ['id' => $competition->id])
            ->assertJsonStructure([
                'data' => [
                    'competition' => [
                        'finances' => [
                            'balance' => [
                                'amount',
                            ],
                        ],
                    ],
                ],
            ]);
    }

    public function testNonManagersCannotSeeSensitiveFields(): void
    {
        /** @var User $nonManager */
        $nonManager = User::factory()->create(['is_manager' => false]);
        $competition = Competition::factory()->create();

        $query = /** @lang GraphQL */ '
            query ($id: ID!){
                competition(id: $id) {
                    finances {
                        balance {
                            amount
                        }
                    }
                }
            }
        ';

        $this->authenticate($nonManager);
        $this->graphQL( $query, ['id' => $competition->id])
            ->assertJSON(self::UNAUTHORIZED_RESPONSE);
    }

    public function testCanQueryRelationships(): void
    {
        /** @var User $user */
        $user = User::factory()->create(['is_manager' => true]);
        $this->authenticate($user);

        $competitor = Competitor::factory()->create();
        $spectator = Spectator::factory()->create();
        $staff = Staff::factory()->create();
        $event = Event::factory()->create();
        $day = Day::factory()->create();

        $competition = Competition::factory()->create(['id' => $this->faker->unique()->uuid()]);

        $competition->competitors()->save($competitor);
        $competition->spectators()->save($spectator);
        $competition->staff()->save($staff);
        $competition->events()->save($event);
        $competition->days()->save($day);

        $this->graphQL(/** @lang GraphQL */ '
            query ($id: ID!){
                competition(id: $id) {
                    id
                    competitors {
                        id
                    }
                    spectators {
                        id
                    }
                    staff {
                        id
                    }
                    events {
                        id
                    }
                    days {
                        id
                    }
                }
            }
        ', [
            'id' => $competition->id
        ])->assertJSON([
            'data' => [
                'competition' => [
                    'id' => $competition->id,
                    'competitors' => [[
                        'id' => $competitor->id,
                    ]],
                    'spectators' => [[
                        'id' => $spectator->id,
                    ]],
                    'staff' => [[
                        'id' => $staff->id,
                    ]],
                    'events' => [[
                        'id' => $event->id,
                    ]],
                    'days' => [[]],
                ]
            ]
        ]);
    }
}

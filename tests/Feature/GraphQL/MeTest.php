<?php

namespace Tests\Feature\GraphQL;

use App\Models\Competition;
use App\Models\Competitor;
use App\Models\Staff;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\GraphQLTestCase;

class MeTest extends GraphQLTestCase
{
    use RefreshDatabase;

    public function testCanQueryMeWhenAuthenticated(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        $this->authenticate($user);

        $this->graphQL(/** @lang GraphQL */ '
            query me{
                me {
                    name
                    wca_id
                }
            }
        ')->assertJSON([
            'data' => [
                'me' => [
                    'name' => $user->name,
                    'wca_id' => $user->wca_id,
                ]
            ]
        ]);
    }

    public function testCanQueryMeWhenNotAuthenticated(): void
    {
        $this->graphQL(/** @lang GraphQL */ '
            query me{
                me {
                    name
                    wca_id
                }
            }
        ')->assertJSON([
            'data' => [
                'me' => null
            ]
        ]);
    }

    public function testCanQueryRegistrationsOnMeQuery(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        $this->authenticate($user);

        $competition = Competition::factory()->create();
        $competitor = Competitor::factory()->competition($competition->id)->create(['wca_id' => $user->wca_id]);
        $staff = Staff::factory()->competition($competition->id)->create(['wca_id' => $user->wca_id]);

        $this->graphQL(/** @lang GraphQL */ '
            query me{
                me {
                    registrations {
                        competition {
                            id
                        }
                        competitor {
                            wca_id
                        }
                        staff {
                            wca_id
                        }
                    }
                }
            }
        ')->assertJSON([
            'data' => [
                'me' => [
                    'registrations' => [[
                        'competition' => [
                            'id' => (string) $competition->id,
                        ],
                        'competitor' => [
                            'wca_id' => $competitor->wca_id,
                        ],
                        'staff' => [
                            'wca_id' => $staff->wca_id,
                        ],
                    ]],
                ],
            ],
        ]);
    }

    public function testCanFilterRegistrationsOnMeQuery(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        $this->authenticate($user);

        $competition1 = Competition::factory()->create();
        $competitor1 = Competitor::factory()->competition($competition1->id)->create(['wca_id' => $user->wca_id]);
        $staff1 = Staff::factory()->competition($competition1->id)->create(['wca_id' => $user->wca_id]);

        $competition2 = Competition::factory()->create();
        $competitor2 = Competitor::factory()->competition($competition2->id)->create(['wca_id' => $user->wca_id]);

        $competition3 = Competition::factory()->create();

        $query = /** @lang GraphQL */ '
            query me ($competitionId: ID){
                me {
                    registrations(competition_id: $competitionId) {
                        competition {
                            id
                        }
                        competitor {
                            wca_id
                        }
                        staff {
                            wca_id
                        }
                    }
                }
            }';

        $this->graphQL($query, ['competitionId' => $competition1->id])->assertJSON([
            'data' => [
                'me' => [
                    'registrations' => [[
                        'competition' => [
                            'id' => (string) $competition1->id,
                        ],
                        'competitor' => [
                            'wca_id' => $competitor1->wca_id,
                        ],
                        'staff' => [
                            'wca_id' => $staff1->wca_id,
                        ],
                    ]],
                ],
            ],
        ]);

        $this->graphQL($query, ['competitionId' => $competition2->id])->assertJSON([
            'data' => [
                'me' => [
                    'registrations' => [[
                        'competition' => [
                            'id' => (string) $competition2->id,
                        ],
                        'competitor' => [
                            'wca_id' => $competitor2->wca_id,
                        ],
                        'staff' => null,
                    ]],
                ],
            ],
        ]);

        $this->graphQL($query, ['competitionId' => $competition3->id])->assertJSON([
            'data' => [
                'me' => [
                    'registrations' => [[
                        'competition' => [
                            'id' => (string) $competition3->id,
                        ],
                        'competitor' => null,
                        'staff' => null,
                    ]],
                ],
            ],
        ]);
    }
}

<?php

namespace Tests\Feature\GraphQL;

use App\Models\Competition;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\GraphQLTestCase;

class CompetitionTest extends GraphQLTestCase
{
    use RefreshDatabase;

    /**
     * A basic test example.
     *
     * @return void
     */
    public function testCanQueryACompetition()
    {
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
}

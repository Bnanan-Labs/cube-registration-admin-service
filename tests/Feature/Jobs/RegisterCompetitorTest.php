<?php

namespace Tests\Feature\Jobs;

use App\Jobs\RegisterCompetitor;
use App\Models\Competitor;
use App\Models\Event;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\GraphQLTestCase;

class RegisterCompetitorTest extends GraphQLTestCase
{
    use RefreshDatabase, WithFaker;

    public function testCanRegisterCompetitor()
    {
        $user = User::factory()->create();
        $events = Event::factory()->times(3)->create();

        $registration = [
            'id' => 1234,
            'first_name' => $this->faker->firstName(),
            'last_name' => $this->faker->lastName(),
            'email' => $this->faker->email(),
            'events' => $events->map(Fn (Event $e) => $e->id),
        ];

        // Assert database empty
        $this->assertDatabaseCount('competitors', 0);

        // dispatchSync job
        RegisterCompetitor::dispatchSync($registration, $user, $this->faker->ipv4());

        // Assert side effect happens
        $this->assertDatabaseCount('competitors', 1);

        $competitor = Competitor::first();
        $this->assertEquals([
            'first_name' => $registration['first_name'],
            'last_name' => $registration['last_name'],
            'gender' => $user->wca->gender,
            'is_delegate' => (bool) $user->wca->delegate_status,
            'wca_teams' => collect($user->wca->teams),
            'avatar' => $user->avatar,
            'wca_id' => $user->wca_id,
            'nationality' => $user->nationality,
            'email' => $user->email,
        ],
            $competitor->only(['first_name', 'last_name', 'gender', 'is_delegate', 'wca_teams', 'avatar', 'wca_id', 'nationality', 'email'])
        );
        $this->assertEquals(3, $competitor->events()->count());
    }

}

<?php

namespace Tests\Feature\Jobs;

use App\Jobs\RegisterCompetitor;
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
        $registration = [
            'id' => 1234,
            'first_name' => $this->faker->firstName(),
            'last_name' => $this->faker->lastName(),
            'email' => $this->faker->email(),
        ];

        // Assert database empty
        $this->assertDatabaseCount('competitors', 0);

        // dispatchSync job
        RegisterCompetitor::dispatchSync($registration, $user);

        // Assert side effect happens
        $this->assertDatabaseCount('competitors', 1);
    }

}

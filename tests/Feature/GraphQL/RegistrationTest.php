<?php

namespace Tests\Feature\GraphQL;

use App\Jobs\RegisterCompetitor;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Laravel\Sanctum\Sanctum;
use Tests\GraphQLTestCase;

class RegistrationTest extends GraphQLTestCase
{
    use RefreshDatabase;

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
            'first_name' => 'First',
            'last_name' => 'Last',
            'email' => 'test@test.com',
            'events' => [],
            'days' => [],
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
}

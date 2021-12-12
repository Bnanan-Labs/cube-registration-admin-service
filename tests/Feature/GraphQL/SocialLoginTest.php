<?php

namespace Tests\Feature\GraphQL;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Socialite\Facades\Socialite;
use Tests\GraphQLTestCase;
use Tests\WithGuzzle;

class SocialLoginTest extends GraphQLTestCase
{
    use RefreshDatabase, WithFaker, WithGuzzle;

    /**
     * A basic test example.
     *
     * @return void
     */
    public function testCanGrantToken()
    {
        $input = [
            'provider' => 'wca',
            'token' => 'cats',
        ];

        $user = [
            'id' => $this->faker()->uuid(),
            'name' => $this->faker()->name(),
            'email' => $this->faker()->email(),
            'avatar' => [
                'url' => $this->faker()->imageUrl(),
            ],
            'wca_id' => $this->faker()->regexify('20[0-2][0-9][A-Z]{4}[0-9]{2}'),
        ];

        $client = $this->makeGuzzleClient([
            $this->makeResponse(['access_token' => 'dogs']),
            $this->makeResponse(['me' => $user]),
        ]);

        Socialite::with('wca')->setHttpClient($client);

        $this->graphQL(/** @lang GraphQL */ '
            mutation ($input: SocialLoginInput!)
            {
                socialLogin(input: $input) {
                    token
                }
            }
        ', [
            'input' => $input,
        ])
            ->assertStatus(200)
            ->assertJsonStructure([
            'data' => [
                'socialLogin' => [
                    'token'
                ]
            ]
        ]);

        $this->assertDatabaseHas(User::class,[
            'name' => $user['name'],
            'wca_id' => $user['wca_id'],
            'email' => $user['email'],
        ]);
    }
}

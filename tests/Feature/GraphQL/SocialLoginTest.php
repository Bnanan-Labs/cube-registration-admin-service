<?php

namespace Tests\Feature\GraphQL;

use App\Enums\Wca;
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
        $user = [
            'id' => $this->faker()->uuid(),
            'name' => $this->faker()->name(),
            'email' => $this->faker()->email(),
            'avatar' => [
                'url' => $this->faker()->imageUrl(),
            ],
            'wca_id' => $this->faker()->regexify(Wca::idRegex->value),
        ];

        $client = $this->makeGuzzleClient([
            $this->makeResponse(['access_token' => 'dogs']),
            $this->makeResponse(['me' => $user]),
        ]);

        Socialite::with('wca')->setHttpClient($client);

        $this->graphQL(/** @lang GraphQL */ '
            mutation ($code: String!)
            {
                socialLogin(code: $code) {
                    token
                }
            }
        ', [
            'code' => 'cats',
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

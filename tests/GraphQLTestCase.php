<?php

namespace Tests;

use App\Models\User;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Laravel\Sanctum\Sanctum;
use Nuwave\Lighthouse\Testing\ClearsSchemaCache;
use Tests\Concerns\MakesGraphQLRequests;

abstract class GraphQLTestCase extends BaseTestCase
{
    use CreatesApplication,
        MakesGraphQLRequests,
        ClearsSchemaCache;

    protected function setUp(): void
    {
        parent::setUp();
        $this->bootClearsSchemaCache();
    }

    /**
     * @param User|null $user
     * @param array $abilities
     * @return User
     */
    protected function authenticate(?User $user = null, array $abilities = []): User
    {
        if (empty($abilities)) {
            $abilities = ['*'];
        }
        if (!$user) {
            $user = User::factory()->create();
        }
        $this->headers = [
            'Authorization' => "Bearer {$user->createToken('test')->plainTextToken}",
        ];
        Sanctum::actingAs($user, $abilities);
        return $user;
    }
}

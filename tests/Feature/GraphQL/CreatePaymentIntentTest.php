<?php

namespace Tests\Feature\GraphQL;

use App\Enums\FinancialEntryType;
use App\Models\Competition;
use App\Models\Competitor;
use App\Models\User;
use App\Services\Finances\MoneyBag;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\GraphQLTestCase;

class CreatePaymentIntentTest extends GraphQLTestCase
{
    use RefreshDatabase, WithFaker;

    public function testCanCreatePaymentIntent(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        $this->authenticate($user);
        $competitor = Competitor::factory()->create(['wca_id' =>  $user->wca_id]);
        $competitor->competition->update(['currency' => 'DKK', 'stripe_api_key' => 'sk_test_4RxUm8rtZ0phcTFLbHvkTJD5']);
        $competitor->finances->entries()->create([
            'type' => FinancialEntryType::baseFee,
            'balance' => new MoneyBag(-500),
        ]);

        $this->graphQL(/** @lang GraphQL */ '
            mutation ($id: ID!){
                createPaymentIntent(competition_id: $id) {
                    intent_secret
                }
            }
        ', [
            'id' => $competitor->competition->id
        ])->assertJSONStructure([
            'data' => [
                'createPaymentIntent' => [
                    'intent_secret'
                ]
            ]
        ]);
    }

    public function testCanFailPaymentIntentIfNothingToPay(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        $this->authenticate($user);
        $competitor = Competitor::factory()->create(['wca_id' =>  $user->wca_id]);
        $competitor->competition->update(['currency' => 'DKK', 'stripe_api_key' => 'sk_test_4RxUm8rtZ0phcTFLbHvkTJD5']);

        $this->graphQL(/** @lang GraphQL */ '
            mutation ($id: ID!){
                createPaymentIntent(competition_id: $id) {
                    intent_secret
                }
            }
        ', [
            'id' => $competitor->competition->id
        ])->assertJSON([
            'errors' => [
                [
                    'message' => "You currently don't have an unpaid balance"
                ]
            ]
        ]);
    }

    public function testCanFailPaymentIntentIfCompetitionDoesNotExist(): void
    {
        $this->authenticate();
        $competitor = Competitor::factory()->create();
        $competitor->competition->update(['currency' => 'DKK', 'stripe_api_key' => 'sk_test_4RxUm8rtZ0phcTFLbHvkTJD5']);

        $this->graphQL(/** @lang GraphQL */ '
            mutation ($id: ID!){
                createPaymentIntent(competition_id: $id) {
                    intent_secret
                }
            }
        ', [
            'id' => 'NOT_VALID_UUID'
        ])->assertJSON([
            'errors' => [
                [
                    'message' => "The requested competition does not exist"
                ]
            ]
        ]);
    }

    public function testCanFailPaymentIntentIfCompetitorDoesNotExist(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        $this->authenticate($user);
        $competition = Competition::factory()->create();

        $this->graphQL(/** @lang GraphQL */ '
            mutation ($id: ID!){
                createPaymentIntent(competition_id: $id) {
                    intent_secret
                }
            }
        ', [
            'id' => $competition->id
        ])->assertJSON([
            'errors' => [
                [
                    'message' => "The requested competitor does not exist"
                ]
            ]
        ]);
    }

    public function testCanGuardCreatePaymentIntent(): void
    {
        $competitor = Competitor::factory()->create();
        $competitor->competition->update(['currency' => 'DKK', 'stripe_api_key' => 'sk_test_4RxUm8rtZ0phcTFLbHvkTJD5']);
        $competitor->finances->entries()->create([
            'type' => FinancialEntryType::baseFee,
            'balance' => new MoneyBag(-500),
        ]);

        $this->graphQL(/** @lang GraphQL */ '
            mutation ($id: ID!){
                createPaymentIntent(competition_id: $id) {
                    intent_secret
                }
            }
        ', [
            'id' => $competitor->competition->id
        ])->assertJSON(self::UNAUTHENTICATED_RESPONSE);
    }

}

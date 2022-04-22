<?php

namespace Tests\Feature\GraphQL;

use App\Enums\FinancialEntryType;
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
        $this->authenticate();
        $competitor = Competitor::factory()->create();
        $competitor->competition->update(['currency' => 'DKK', 'stripe_api_key' => 'sk_test_4RxUm8rtZ0phcTFLbHvkTJD5']);
        $competitor->finances->entries()->create([
            'type' => FinancialEntryType::baseFee,
            'balance' => new MoneyBag(-500),
        ]);

        $this->graphQL(/** @lang GraphQL */ '
            mutation ($id: ID!){
                createPaymentIntent(competitor_id: $id) {
                    intent_secret
                }
            }
        ', [
            'id' => $competitor->id
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
        $this->authenticate();
        $competitor = Competitor::factory()->create();
        $competitor->competition->update(['currency' => 'DKK', 'stripe_api_key' => 'sk_test_4RxUm8rtZ0phcTFLbHvkTJD5']);

        $this->graphQL(/** @lang GraphQL */ '
            mutation ($id: ID!){
                createPaymentIntent(competitor_id: $id) {
                    intent_secret
                }
            }
        ', [
            'id' => $competitor->id
        ])->assertJSON([
            'errors' => [
                [
                    'message' => "You currently don't have an unpaid balance"
                ]
            ]
        ]);
    }

    public function testCanFailPaymentIntentIfCompetitorDoesNotExist(): void
    {
        $this->authenticate();
        $competitor = Competitor::factory()->create();
        $competitor->competition->update(['currency' => 'DKK', 'stripe_api_key' => 'sk_test_4RxUm8rtZ0phcTFLbHvkTJD5']);

        $this->graphQL(/** @lang GraphQL */ '
            mutation ($id: ID!){
                createPaymentIntent(competitor_id: $id) {
                    intent_secret
                }
            }
        ', [
            'id' => 'NOT_VALID_UUID'
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
                createPaymentIntent(competitor_id: $id) {
                    intent_secret
                }
            }
        ', [
            'id' => $competitor->id
        ])->assertJSON(self::UNAUTHENTICATED_RESPONSE);
    }

}

<?php

namespace Tests\Feature\GraphQL;

use App\Models\Competitor;
use App\Models\FinancialBook;
use App\Services\Finances\MoneyBag;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\GraphQLTestCase;

class FinancialBookTest extends GraphQLTestCase
{
    use RefreshDatabase, WithFaker;

    public function testCanShowMoneyBagFields()
    {
        $book = FinancialBook::factory()->create([
            'balance' => new MoneyBag(amount: $this->faker->numberBetween(-2000,2000)),
            'paid' => new MoneyBag(amount: $this->faker->numberBetween(1,2000)),
            'total' => new MoneyBag(amount: $this->faker->numberBetween(1,2000)),
        ]);
        $competitor = Competitor::factory()->create(['financial_book_id' => $book->id]);

        $this->authenticate();

        $this->graphQL(/** @lang GraphQL */ '
            query competitor($id: ID!){
                competitor(id: $id) {
                    finances {
                        balance {
                            currency
                            amount
                        }
                        paid {
                            currency
                            amount
                        }
                        total {
                            currency
                            amount
                        }
                    }
                }
            }
        ', [
            'id' => $competitor->id
        ])->assertJSON([
            'data' => [
                'competitor' => [
                    'finances' => [
                        'balance' => [
                            'currency' => $book->balance->currency,
                            'amount' => $book->balance->amount,
                        ],
                        'paid' => [
                            'currency' => $book->paid->currency,
                            'amount' => $book->paid->amount,
                        ],
                        'total' => [
                            'currency' => $book->total->currency,
                            'amount' => $book->total->amount,
                        ],
                    ],
                ],
            ],
        ]);
    }

    public function testFinancesAreGuarded()
    {
        $book = FinancialBook::factory()->create([
            'balance' => new MoneyBag(amount: $this->faker->numberBetween(-2000,2000)),
            'paid' => new MoneyBag(amount: $this->faker->numberBetween(1,2000)),
        ]);
        $competitor = Competitor::factory()->create(['financial_book_id' => $book->id]);
        $query = /** @lang GraphQL */ '
            query competitor($id: ID!){
                competitor(id: $id) {
                    finances {
                        balance {
                            currency
                        }
                    }
                }
            }
        ';

        $this->graphQL($query, [
            'id' => $competitor->id
        ])->assertJSON([
            'errors' => [
                [
                    'message' => 'Unauthenticated.',
                ],
            ],
        ]);
    }
}

<?php

namespace Tests\Feature\GraphQL;

use App\Models\Competition;
use App\Models\Day;
use App\Models\User;
use App\Services\Finances\MoneyBag;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Tests\GraphQLTestCase;

class DayTest extends GraphQLTestCase
{
    use RefreshDatabase, WithFaker;

    public function testCanViewAllFields()
    {
        Competition::factory()->create();
        $day = Day::factory()->create();

        $this->graphQL(/** @lang GraphQL */ '
            query ($id: ID!) {
                day(id: $id) {
                    title
                    week_day
                    sort_id
                    date
                    price {
                        currency
                        amount
                    }
                    is_bookable
                }
            }
        ', [
            'id' => $day->id,
        ])->assertJSON([
            'data' => [
                'day' => [
                    'title' => $day->title,
                    'sort_id' => $day->sort_id,
                    'date' => $day->date->format('Y-m-d'),
                    'price' => [
                        'amount' => $day->price->amount
                    ],
                    'is_bookable' => $day->is_bookable,
                ],
            ],
        ]);
    }

    public function testCanCreateEndpoint()
    {
        Competition::factory()->create();
        /** @var User $user */
        $user = User::factory()->create(['is_manager' => true]);
        $this->authenticate($user);

        $input = [
            'title' => $this->faker()->firstName(),
            'price' => new MoneyBag(amount: $this->faker()->numberBetween(1,100)),
            'date' => $this->faker()->date(),
            'is_bookable' => $this->faker()->boolean(),
        ];

        $this->graphQL(/** @lang GraphQL */ '
            mutation ($input: CreateDayInput!){
                createDay(input: $input) {
                    title
                    week_day
                    sort_id
                    date
                    price {
                        currency
                        amount
                    }
                    is_bookable
                }
            }
        ', [
            'input' => $input,
        ])->assertJSON([
            'data' => [
                'createDay' => [
                    'title' => $input['title'],
                    'week_day' => Str::upper(Carbon::createFromFormat('Y-m-d', $input['date'])->format('l')),
                    'date' => $input['date'],
                    'price' => [
                        'amount' => $input['price']->amount
                    ],
                    'is_bookable' => $input['is_bookable'],
                ],
            ],
        ]);
    }

    public function testCanUpdateEndpoint()
    {
        Competition::factory()->create();
        /** @var User $user */
        $user = User::factory()->create(['is_manager' => true]);
        $this->authenticate($user);

        $input = [
            'title' => $this->faker()->firstName(),
            'price' => new MoneyBag(amount: $this->faker()->numberBetween(1,100)),
            'date' => $this->faker()->date(),
            'is_bookable' => $this->faker()->boolean(),
        ];

        $this->graphQL(/** @lang GraphQL */ '
            mutation ($input: CreateDayInput!){
                createDay(input: $input) {
                    title
                    week_day
                    sort_id
                    date
                    price {
                        currency
                        amount
                    }
                    is_bookable
                }
            }
        ', [
            'input' => $input,
        ])->assertJSON([
            'data' => [
                'createDay' => [
                    'title' => $input['title'],
                    'week_day' => Str::upper(Carbon::createFromFormat('Y-m-d', $input['date'])->format('l')),
                    'date' => $input['date'],
                    'price' => [
                        'amount' => $input['price']->amount
                    ],
                    'is_bookable' => $input['is_bookable'],
                ],
            ],
        ]);
    }

    public function testCanDeleteEndpoint()
    {
        Competition::factory()->create();
        /** @var User $user */
        $user = User::factory()->create(['is_manager' => true]);
        $this->authenticate($user);
        $day = Day::factory()->create();

        $this->assertModelExists($day);

        $this->graphQL(/** @lang GraphQL */ '
            mutation ($id: ID!){
                deleteDay(id: $id) {
                    id
                }
            }
        ', [
            'id' => $day->id,
        ])->assertJSON([
            'data' => [
                'deleteDay' => [
                    'id' => $day->id,
                ],
            ],
        ]);

        $this->assertModelMissing($day);
    }
}

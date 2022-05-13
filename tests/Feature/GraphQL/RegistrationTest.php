<?php

namespace Tests\Feature\GraphQL;

use App\Enums\PaymentStatus;
use App\Enums\RegistrationStatus;
use App\Jobs\CreateCompetitorBook;
use App\Jobs\RegisterCompetitor;
use App\Models\Competition;
use App\Models\Competitor;
use App\Models\Event;
use App\Models\Payment;
use App\Models\User;
use App\Services\Finances\MoneyBag;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\GraphQLTestCase;

class RegistrationTest extends GraphQLTestCase
{
    use RefreshDatabase, WithFaker;

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
        Competition::factory()->create(['registration_starts' => now()->subDays(10)]);
        Queue::fake();
        Queue::assertNothingPushed();
        $registration = [
            'first_name' => $this->faker->firstName(),
            'last_name' => $this->faker->lastName(),
            'email' => $this->faker->email(),
            'guests' => [$this->faker->name(), $this->faker->name()],
            'is_interested_in_nations_cup' => $this->faker->boolean(),
            'events' => [$this->faker->numberBetween(1,10), $this->faker->numberBetween(1,10)],
            'days' => [$this->faker->numberBetween(1,10), $this->faker->numberBetween(1,10)],
        ];

        $this->graphQL(/** @lang GraphQL */ '
            mutation ($input: RegisterCompetitorInput!)
            {
                registerCompetitor(input: $input) {
                    registration_id
                }
            }
        ', [
            'input' => $registration
        ])->assertJsonStructure([
            'data' => [
                'registerCompetitor' => [
                    'registration_id'
                ]
            ]
        ]);

        Queue::assertPushed(Fn (RegisterCompetitor $job) => $job->registration['email'] === $registration['email']);
        Queue::assertPushed(Fn (RegisterCompetitor $job) => $job->user['wca_id'] === $user->wca_id);
    }

    /**
     * A basic test example.
     *
     * @return void
     */
    public function testCanGuardRegistrationBeforeStart()
    {
        /** @var User $user */
        $user = User::factory()->create();
        $this->authenticate($user);
        Competition::factory()->create(['registration_starts' => now()->addMinutes(2)]);
        Queue::fake();
        Queue::assertNothingPushed();
        $registration = [
            'first_name' => $this->faker->firstName(),
            'last_name' => $this->faker->lastName(),
            'email' => $this->faker->email(),
            'guests' => [$this->faker->name(), $this->faker->name()],
            'is_interested_in_nations_cup' => $this->faker->boolean(),
            'events' => [$this->faker->numberBetween(1,10), $this->faker->numberBetween(1,10)],
            'days' => [$this->faker->numberBetween(1,10), $this->faker->numberBetween(1,10)],
        ];

        $this->graphQL(/** @lang GraphQL */ '
            mutation ($input: RegisterCompetitorInput!)
            {
                registerCompetitor(input: $input) {
                    registration_id
                }
            }
        ', [
            'input' => $registration
        ])->assertJson([
            'errors' => [
                [
                    'message' => 'Registration for this competition has not opened yet',
                ],
            ],
        ]);

        Queue::assertNothingPushed();
    }

    /**
     * A basic test example.
     *
     * @return void
     */
    public function testCanRejectRegistration()
    {
        /** @var User $user */
        $user = User::factory()->create(['wca_id' => '2010TEST01']);
        $this->authenticate($user);
        Competitor::factory()->create(['wca_id' => '2010TEST01']);
        Queue::fake();
        Queue::assertNothingPushed();
        $registration = [
            'first_name' => $this->faker->firstName(),
            'last_name' => $this->faker->lastName(),
            'email' => $this->faker->email(),
            'guests' => [$this->faker->name(), $this->faker->name()],
            'is_interested_in_nations_cup' => $this->faker->boolean(),
            'events' => [$this->faker->numberBetween(1,10), $this->faker->numberBetween(1,10)],
            'days' => [$this->faker->numberBetween(1,10), $this->faker->numberBetween(1,10)],
        ];

        $this->graphQL(/** @lang GraphQL */ '
            mutation ($input: RegisterCompetitorInput!)
            {
                registerCompetitor(input: $input) {
                    registration_id
                }
            }
        ', [
            'input' => $registration
        ])->assertJSON([
            'errors' => [
                [
                    'message' => 'You already have a pending registration',
                ],
            ],
        ]);

        Queue::assertNothingPushed();
    }

    /**
     * A basic test example.
     *
     * @return void
     */
    public function testCanApproveRegistrations()
    {
        /** @var User $user */
        $user = User::factory()->manager()->create();
        $this->authenticate($user);
        $competitor = Competitor::factory()->create(['wca_id' => '2010TEST01', 'registration_status' => RegistrationStatus::pending]);

        $this->graphQL(/** @lang GraphQL */ '
            mutation ($id: ID!)
            {
                approveRegistration(id: $id) {
                    id
                    registration_status
                }
            }
        ', [
            'id' => $competitor->id
        ])->assertJSON([
            'data' => [
                'approveRegistration' => [
                    'id' => $competitor->id,
                    'registration_status' => RegistrationStatus::accepted->value,
                ],
            ],
        ]);
    }


    public function testCanFailApproveRegistrationsIfInvalidUser()
    {
        /** @var User $user */
        $user = User::factory()->manager()->create();
        $this->authenticate($user);
        $competitor = Competitor::factory()->create(['wca_id' => '2010TEST01', 'registration_status' => RegistrationStatus::pending]);

        $this->graphQL(/** @lang GraphQL */ '
            mutation ($id: ID!)
            {
                approveRegistration(id: $id) {
                    id
                    registration_status
                }
            }
        ', [
            'id' => 'BOGUS-ID'
        ])->assertJSON([
            'errors' => [[
                'message' => 'Competitor could not be found',
            ]],
        ]);
    }

    public function testCanCancelRegistrationWithPartialRefund()
    {
        /** @var User $user */
        $user = User::factory()->manager()->create();
        $this->authenticate($user);
        $competitor = Competitor::factory()->accepted()->create(['wca_id' => '2010TEST01']);
        $event = Event::factory()->create();
        $competitor->events()->save($event);
        CreateCompetitorBook::dispatchSync($competitor);
        $competitor->refresh();
        $competitor->finances->payments()->create([
            'intent_id' => 'bogus',
            'intent_secret' => 'bogus',
            'total' => new MoneyBag(-$competitor->finances->total->amount),
            'extra' => 'Registration Payment'
        ]);
        $competitor->finances->payments->first()->markAsPaid();
        $competitor->refresh();

        $this->assertCount(1, $competitor->events);
        $this->assertNotNull($competitor->approved_at);

        $this->graphQL(/** @lang GraphQL */ '
            mutation ($id: ID!)
            {
                cancelRegistration(id: $id) {
                    id
                    registration_status
                    payment_status
                    events {
                        id
                    }
                }
            }
        ', [
            'id' => $competitor->id
        ])->assertJSON([
            'data' => [
                'cancelRegistration' => [
                    'id' => $competitor->id,
                    'registration_status' => RegistrationStatus::cancelled->value,
                    'payment_status' => PaymentStatus::needsPartialRefund->value,
                    'events' => [],
                ],
            ],
        ]);

        $competitor->refresh();
        $this->assertCount(0, $competitor->events);
        $this->assertNull($competitor->approved_at);
    }

    public function testCanCancelRegistrationWithFreeRegistration()
    {
        /** @var User $user */
        $user = User::factory()->manager()->create();
        $this->authenticate($user);
        $competitor = Competitor::factory()->accepted()->create(['wca_id' => '2010TEST01', 'is_exempt_from_payment' => true]);
        $event = Event::factory()->create();
        $competitor->events()->save($event);
        CreateCompetitorBook::dispatchSync($competitor);
        $competitor->refresh();

        $this->assertCount(1, $competitor->events);
        $this->assertNotNull($competitor->approved_at);
        $this->assertEquals(0, $competitor->finances->balance->amount);

        $this->graphQL(/** @lang GraphQL */ '
            mutation ($id: ID!)
            {
                cancelRegistration(id: $id) {
                    id
                    registration_status
                    payment_status
                    events {
                        id
                    }
                }
            }
        ', [
            'id' => $competitor->id
        ])->assertJSON([
            'data' => [
                'cancelRegistration' => [
                    'id' => $competitor->id,
                    'registration_status' => RegistrationStatus::cancelled->value,
                    'payment_status' => PaymentStatus::paid->value,
                    'events' => [],
                ],
            ],
        ]);

        $competitor->refresh();
        $this->assertCount(0, $competitor->events);
        $this->assertNull($competitor->approved_at);
    }
}

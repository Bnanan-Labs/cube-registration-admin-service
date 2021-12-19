<?php

namespace Tests\Feature\GraphQL;

use App\Enums\ShirtSize;
use App\Enums\Wca;
use App\Models\Staff;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\GraphQLTestCase;

class StaffTest extends GraphQLTestCase
{
    use RefreshDatabase, WithFaker;

    public function testCanViewAllFields()
    {
        /** @var User $user */
        $user = User::factory()->manager()->create();
        $this->authenticate($user);

        $staff = Staff::factory()->create();

        $this->graphQL(/** @lang GraphQL */ '
            query ($id: ID!) {
                staff(id: $id) {
                    first_name
                    last_name
                    wca_id
                    application
                    registration_status
                    t_shirt_size
                }
            }
        ', [
            'id' => $staff->id,
        ])->assertJSON([
            'data' => [
                'staff' => [
                    'first_name' => $staff->first_name,
                    'last_name' => $staff->last_name,
                    'wca_id' => $staff->wca_id,
                    'application' => $staff->application,
                    'registration_status' => $staff->registration_status->value,
                    't_shirt_size' => $staff->t_shirt_size->value,
                ],
            ],
        ]);
    }

    public function testCanCreateEndpoint()
    {
        /** @var User $user */
        $user = User::factory()->manager()->create();
        $this->authenticate($user);

        $input = [
            'first_name' => $this->faker()->firstName(),
            'last_name' => $this->faker()->lastName(),
            'application' => $this->faker()->text(),
            'wca_id' => $this->faker()->regexify(Wca::idRegex->value),
            't_shirt_size' => $this->faker()->randomElement(ShirtSize::cases()),
        ];

        $this->graphQL(/** @lang GraphQL */ '
            mutation ($input: CreateStaffInput!){
                createStaff(input: $input) {
                    first_name
                    last_name
                    application
                    wca_id
                    t_shirt_size
                }
            }
        ', [
            'input' => $input,
        ])->assertJSON([
            'data' => [
                'createStaff' => [
                    'first_name' => $input['first_name'],
                    'last_name' => $input['last_name'],
                    'application' => $input['application'],
                    'wca_id' => $input['wca_id'],
                    't_shirt_size' => $input['t_shirt_size']->value,
                ],
            ],
        ]);
    }
}

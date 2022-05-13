<?php

namespace App\GraphQL\Mutations;

use App\Enums\RegistrationStatus;
use App\Models\Competitor;
use GraphQL\Error\Error;

final class CancelRegistration
{
    /**
     * @param null $_
     * @param array{} $args
     * @return mixed
     * @throws Error
     */
    public function __invoke($_, array $args)
    {
        if (!($competitor = Competitor::find($args['id']))) {
            throw new Error('Competitor could not be found');
        }

        $competitor->cancel();
        $competitor->refresh();

        return $competitor;
    }
}

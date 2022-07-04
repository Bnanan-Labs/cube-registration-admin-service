<?php

namespace App\GraphQL\Mutations;

use App\Models\Competitor;
use GraphQL\Error\Error;

final class SettleCompetitorBook
{
    /**
     * @param null $_
     * @param array{} $args
     * @return mixed
     * @throws Error
     */
    public function __invoke($_, array $args)
    {
        if (! $competitor = Competitor::find($args['competitor_id' ?? null])) {
            throw new Error('Competitor could not be found');
        }

        dispatch_sync(new \App\Jobs\ProcessRefund($competitor));

        return $competitor;
    }
}

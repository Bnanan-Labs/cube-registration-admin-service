<?php

namespace App\GraphQL\Resolvers;

use App\Models\Event;
use App\Models\User;
use GraphQL\Error\Error;
use GraphQL\Type\Definition\ResolveInfo;
use Illuminate\Support\Collection;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class PsychBuilder
{

    /**
     * @param User|null $rootValue
     * @param array $args
     * @param GraphQLContext $context
     * @param ResolveInfo $resolveInfo
     * @return mixed
     * @throws Error
     */
    function byEvent(?User $rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        if (!$event = Event::find($args['event_id'])) {
            throw new Error('Event could not be found');
        }

        return $event->competitors()->orderBy('best_single')->whereNotNull('best_single');
    }
}

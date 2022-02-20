<?php

namespace App\GraphQL\Queries;

use App\Models\Competition;
use App\Models\User;
use GraphQL\Error\Error;
use GraphQL\Type\Definition\ResolveInfo;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class Me
{
    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function __invoke($_, array $args)
    {
        return Auth::user();
    }

    /**
     * @param User $rootValue
     * @param array $args
     * @param GraphQLContext $context
     * @param ResolveInfo $resolveInfo
     * @return Collection
     * @throws Error
     */
    function registrations(User $rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo): Collection
    {
        $user = $rootValue;
        if ($competitionId = $args['competition_id'] ?? false) {
            if (!($competition = Competition::find($competitionId)) ) {
                throw new Error("Competition with id '{$competitionId}' could not be found");
            }

            $staff = $user->staffs()->where('competition_id', '=', $competitionId)->first();
            $competitor = $user->competitors()->where('competition_id', '=', $competitionId)->first();
            return collect([
                [
                    'competition' => $competition,
                    'competitor' => $competitor,
                    'staff' => $staff,
                ],
            ]);
        }

        $competitors = $user->competitors->groupBy('competition_id');
        $staffs = $user->staffs->groupBy('competition_id');

        return $user->competitions->map(function (Competition $competition) use ($competitors, $staffs) {
            return [
                'competition' => $competition,
                'competitor' => $competitors->get($competition->id)?->first(),
                'staff' => $staffs->get($competition->id)?->first(),
            ];
        });
    }
}

<?php

namespace App\GraphQL\Mutations;

use App\Models\Competition;
use GraphQL\Error\Error;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Str;

class RegisterCompetitor
{
    /**
     * @param null $_
     * @param array<string, mixed> $args
     * @throws Error
     */
    public function __invoke($_, array $args)
    {
        /* @var $user \App\Models\User */
        $user = Auth::user();
        if ($user->is_competitor) {
            throw new Error('You already have a pending registration');
        }
        if ((Competition::first())->registration_starts > now()) {
            throw new Error('Registration for this competition has not opened yet');
        }

        $registrationId = Str::uuid();

        \App\Jobs\RegisterCompetitor::dispatch($args, $user, $registrationId, Request::ip());

        return [
            'registration_id' => $registrationId,
            'first_name' => $args['first_name'],
            'last_name' => $args['last_name'],
            'wca_id' => $user->wca_id,
            'email' => $args['email'],
        ];
    }
}

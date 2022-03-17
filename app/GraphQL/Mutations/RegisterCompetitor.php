<?php

namespace App\GraphQL\Mutations;

use GraphQL\Error\Error;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

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

        \App\Jobs\RegisterCompetitor::dispatch($args, $user, Request::ip());

        return [
            'id' => 1234,
            'first_name' => $args['first_name'],
            'last_name' => $args['last_name'],
            'wca_id' => $user->wca_id,
            'email' => $args['email'],
        ];
    }
}

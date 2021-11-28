<?php

namespace App\GraphQL\Mutations;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class RegisterCompetitor
{
    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function __invoke($_, array $args)
    {
        \App\Jobs\RegisterCompetitor::dispatch($args, Auth::user(), Request::ip());

        return [
            'id' => 1234,
            'first_name' => $args['first_name'],
            'last_name' => $args['last_name'],
            'wca_id' => Auth::user()->wca_id,
            'email' => $args['email'],
        ];
    }
}

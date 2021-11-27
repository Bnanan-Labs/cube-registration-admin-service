<?php

namespace App\GraphQL\Mutations;

class RegisterCompetitor
{
    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function __invoke($_, array $args)
    {
        \App\Jobs\RegisterCompetitor::dispatch($args);

        return [
            'id' => 1234,
            'first_name' => $args['first_name'],
            'last_name' => $args['last_name'],
            'wca_id' => '2012AAAA02',
            'email' => $args['email'],
        ];
    }
}

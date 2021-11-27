<?php

namespace Tests\Concerns;


use Illuminate\Contracts\Config\Repository as ConfigRepository;

/**
 * Testing helpers for making requests to the GraphQL endpoint.
 *
 * @mixin \Illuminate\Foundation\Testing\Concerns\MakesHttpRequests
 */
trait MakesGraphQLRequests {
    protected array $headers = [];

    /**
     * Execute a query as if it was sent as a request to the server.
     *
     * @param  string  $query  The GraphQL query to send
     * @param  array<string, mixed>  $variables  The variables to include in the query
     * @param  array<string, mixed>  $extraParams  Extra parameters to add to the JSON payload
     * @return \Illuminate\Testing\TestResponse
     */
    protected function graphQL(string $query, array $variables = [], array $extraParams = [])
    {
        $params = ['query' => $query];

        if ($variables !== []) {
            $params += ['variables' => $variables];
        }

        $params += $extraParams;

        return $this->postGraphQL($params);
    }

    /**
     * Execute a POST to the GraphQL endpoint.
     *
     * Use this over graphQL() when you need more control or want to
     * test how your server behaves on incorrect inputs.
     *
     * @param  array<mixed, mixed>  $data
     * @param  array<string, string>  $headers
     * @return \Illuminate\Testing\TestResponse
     */
    protected function postGraphQL(array $data, array $headers = [])
    {
        return $this->postJson(
            $this->graphQLEndpointUrl(),
            $data,
            $this->headers,
        );
    }

    /**
     * Return the full URL to the GraphQL endpoint.
     */
    protected function graphQLEndpointUrl(): string
    {
        /** @var ConfigRepository $config */
        $config = app(ConfigRepository::class);

        return route($config->get('lighthouse.route.name'));
    }
}

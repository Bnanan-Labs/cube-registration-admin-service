<?php

namespace Tests;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

trait WithGuzzle
{
    protected $app;
    protected array $historyContainer;

    /**
     * @before
     */
    public function setUpGuzzle()
    {
        $this->historyContainer = [];
    }

    /**
     * @param array|object $data
     * @param int $code
     * @param array $headers
     * @return Response
     */
    protected function makeResponse(array|object $data, int $code = 200, array $headers = []): Response
    {
        return new Response(
            $code,
            array_merge(['Content-Type' => 'application/json'], $headers),
            json_encode($data));
    }

    /**
     * @param array $responses
     * @param array|null $historyContainer
     * @param string|null $inject
     * @return Client
     */
    protected function makeGuzzleClient(array $responses, ?array &$historyContainer = null, ?string $inject = null): Client
    {
        $historyContainer = is_array($historyContainer) ? $historyContainer : $this->historyContainer;

        $handlerStack = HandlerStack::create(new MockHandler($responses));
        $handlerStack->push(Middleware::history($historyContainer));
        $client = new Client(['handler' => $handlerStack]);

        if ($inject) {
            $this->app->when($inject)
                ->needs(Client::class)
                ->give(fn () => $client);
        }

        return $client;
    }

    /**
     * @param int $count
     */
    protected function assertGuzzleCallCount(int $count): void
    {
        $this->assertCount($count, $this->historyContainer);
    }

    /**
     * @param int $index
     * @return Request|null
     */
    protected function getGuzzleRequest(int $index): ?Request
    {
        $transaction = Arr::get($this->historyContainer, $index);
        if ($transaction) {
            return $transaction['request'];
        }
        return null;
    }

    /**
     * @return Collection
     */
    protected function getGuzzleRequests(): Collection
    {
        return collect($this->historyContainer)->map(fn ($transaction) => $transaction['request']);
    }
}

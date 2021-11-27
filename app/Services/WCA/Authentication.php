<?php

namespace App\Services\WCA;

use GuzzleHttp\Client;

class Authentication
{
    protected $client;

    public function __construct()
    {
        $this->client = new Client(['base_uri' => 'https://www.worldcubeassociation.org/']);
    }

    public function getAccessToken($code, $redirect_uri="https://cube.zone/wca")
    {
        return json_decode($this->authorizeCode($code, $redirect_uri)->getBody()->getContents());
    }

    public function authorizeCode($code, $redirect_uri)
    {
        return $this->client->post('oauth/token', [
            'form_params' => [
                'grant_type' => 'authorization_code',
                'client_id' => config('services.wca.client_id'),
                'client_secret' => config('services.wca.client_secret'),
                'code' => $code,
                'redirect_uri' => config('services.wca.redirect'),
            ]
        ]);
    }
}

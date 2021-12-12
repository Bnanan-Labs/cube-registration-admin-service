<?php

namespace App\Socialite\Wca;

use Illuminate\Support\Arr;
use JetBrains\PhpStorm\ArrayShape;
use Laravel\Socialite\Two\AbstractProvider;
use Laravel\Socialite\Two\ProviderInterface;
use Laravel\Socialite\Two\User;

class Provider extends AbstractProvider implements ProviderInterface
{

    /**
     * Get the authentication URL for the provider.
     *
     * @param string $state
     * @return string
     */
    protected function getAuthUrl($state): string
    {
        return $this->buildAuthUrlFromBase('https://www.worldcubeassociation.org/oauth/authorize', $state);
    }

    /**
     * Get the token URL for the provider.
     *
     * @return string
     */
    protected function getTokenUrl(): string
    {
        return 'https://www.worldcubeassociation.org/oauth/token';
    }

    /**
     * Get the token URL for the provider.
     *
     * @return string
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    protected function getTokenFromCode(?string $code): mixed
    {
        $response = $this->getHttpClient()->post(
            $this->getTokenUrl(),
            [
                'form_params' => [
                    'grant_type' => 'authorization_code',
                    'client_id' => config('services.wca.client_id'),
                    'client_secret' => config('services.wca.client_secret'),
                    'code' => $code,
                    'redirect_uri' => config('services.wca.redirect'),
                ]
            ]
        );
        return json_decode($response->getBody(), true)['access_token'];
    }

    /**
     * Get the raw user for the given access token.
     *
     * @param string $token
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    protected function getUserByToken($token): array
    {
        $userUrl = 'https://www.worldcubeassociation.org/api/v0/me';

        $response = $this->getHttpClient()->get(
            $userUrl, $this->getRequestOptions($token)
        );

        return json_decode($response->getBody(), true);
    }

    /**
     * Map the raw user array to a Socialite User instance.
     *
     * @param array $user
     * @return User
     */
    protected function mapUserToObject(array $user): User
    {
        $user = $user['me'];
        return (new User)->setRaw($user)->map([
            'id' => $user['id'],
            'name' => Arr::get($user, 'name'),
            'email' => Arr::get($user, 'email'),
            'avatar' => $user['avatar']['url'],
            'wca_id' => $user['wca_id'],
        ]);
    }

    /**
     * Get the default options for an HTTP request.
     *
     * @param string $token
     * @return array
     */
    #[ArrayShape(['headers' => "string[]"])]
    protected function getRequestOptions(string $token): array
    {
        return [
            'headers' => [
                'Authorization' => "Bearer {$token}",
            ],
        ];
    }

    /**
     * Get the POST fields for the token request.
     *
     * @param  string  $code
     * @return array
     */
    protected function getTokenFields($code): array
    {
        return parent::getTokenFields($code) + ['grant_type' => 'authorization_code'];
    }
}

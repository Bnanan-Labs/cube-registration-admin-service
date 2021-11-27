<?php

namespace App\GraphQL\Mutations;

use App\Models\User;
use App\Services\WCA\Authentication;
use GraphQL\Type\Definition\ResolveInfo;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class SocialLogin
{
    public function __construct(protected Authentication $wcaAuthentication, protected Request $request)
    {
        //
    }

    /**
     * Return a value for the field.
     *
     * @param null $rootValue Usually contains the result returned from the parent field. In this case, it is always `null`.
     * @param mixed[] $args The arguments that were passed into the field.
     * @param GraphQLContext $context Arbitrary data that is shared between all fields of a single query.
     * @param ResolveInfo $resolveInfo Information about the query itself, such as the execution state, the field name, path to the field from the root, and more.
     * @return mixed
     */
    public function __invoke($rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        $this->request->merge(['code' => Arr::get($args, 'token')]);
        $socialUser = Socialite::driver('wca')->stateless()->user('test');

        $user = User::firstOrCreate([
            'wca_id' => $socialUser->user['wca_id'],
        ], [
            'name' => $socialUser->name,
            'email' => $socialUser->email,
            'avatar' => $socialUser->avatar,
            'password' => Hash::make(Str::random(10)),
            'nationality' => $socialUser->user['country_iso2'] ?? '',
            'gender' => $socialUser->user['gender'] ?? '',
            'raw' => json_encode($socialUser->user),
        ]);

        return [
            'token' => $user->createToken('cubing-registration')->plainTextToken,
            'user' => $user,
        ];

//        $args['token'] = $this->wcaAuthentication->getAccessToken($args['token'])->access_token;
//        $credentials = $this->buildCredentials($args, 'social_grant');
//        $response = $this->makeRequest($credentials);
//        $model = app(config('auth.providers.users.model'));
//        $user = $model->where('id', Auth::user()->id)->firstOrFail();
//        $response['user'] = $user;

    }
}

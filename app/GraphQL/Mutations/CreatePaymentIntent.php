<?php

namespace App\GraphQL\Mutations;

use App\Models\Competition;
use App\Services\Finances\MoneyBag;
use GraphQL\Error\Error;
use Illuminate\Support\Facades\Auth;
use Stripe\Exception\ApiConnectionException;
use Stripe\PaymentIntent;
use Stripe\Stripe;

class CreatePaymentIntent
{
    /**
     * @param null $_
     * @param array<string, mixed> $args
     * @throws Error
     */
    public function __invoke($_, array $args)
    {

        $competition = Competition::find($args['competition_id']);

        if (!$competition) {
            throw new Error('The requested competition does not exist');
        }

        $user = Auth::user();
        $competitor = $user->competitors()->where('competition_id', '=', $competition->id)->first();

        if (!$competitor) {
            throw new Error('The requested competitor does not exist');
        }

        if (($amount = -$competitor->finances->balance->amount) <= 0) {
            throw new Error("You currently don't have an unpaid balance");
        }

        try {
            Stripe::setApiKey($competitor->competition->stripe_api_key);
            $intent = PaymentIntent::create([
                'amount' => $amount,
                'currency' => $competitor->competition->currency,
                'description' => "Registration payment for {$competitor->competition->title}",
                'automatic_payment_methods' => [
                    'enabled' => true,
                ],
            ]);

            return $competitor->finances->payments()->create([
                'intent_id' => $intent->id,
                'intent_secret' => $intent->client_secret,
                'total' => new MoneyBag($amount),
                'extra' => 'Registration Payment'
            ]);
        } catch (ApiConnectionException $e) {
            throw new Error('We could not process the payment intent due to: ' . $e->getMessage());
        }
    }
}

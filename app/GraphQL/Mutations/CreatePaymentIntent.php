<?php

namespace App\GraphQL\Mutations;

use App\Models\Competitor;
use App\Models\Payment;
use App\Services\Finances\MoneyBag;
use GraphQL\Error\Error;
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
        $competitor = Competitor::find($args['competitor_id']);

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
                'automatic_payment_methods' => [
                    'enabled' => true,
                ],
            ]);

            return $competitor->finances->payments()->create([
                'intent_secret' => $intent->client_secret,
                'total' => new MoneyBag($amount),
                'extra' => 'Registration Payment'
            ]);
        } catch (ApiConnectionException $e) {
            throw new Error('We could not process the payment intent due to: ' . $e->getMessage());
        }
    }
}

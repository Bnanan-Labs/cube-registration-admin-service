<?php

namespace App\Services\Finances;

use App\Models\Payment;

class PaymentService
{
    public function __construct()
    {
        //
    }

    public function handle(Payment $payment, string $status): void
    {
        switch ($status) {
            case 'succeeded':
                if ($payment->captured_at) {
                    return;
                }
                $payment->markAsPaid();
                return;
            case 'canceled':
            case 'payment_failed':
                $payment->cancel();
                return;
        }
    }
}

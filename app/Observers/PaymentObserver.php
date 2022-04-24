<?php

namespace App\Observers;

use App\Enums\PaymentStatus;
use App\Models\Competitor;
use App\Models\Payment;

class PaymentObserver
{
    /**
     * Handle the Payment "Updated" event.
     *
     * @param Payment $payment
     * @return void
     */
    public function updated(Payment $payment): void
    {
        if ($payment->isDirty('booked_at') && $payment->booked_at) {
            $status = match ($payment->book->balance->amount <=> 0) {
                -1 => PaymentStatus::partiallyPaid,
                0 => PaymentStatus::paid,
                1 => PaymentStatus::needsPartialRefund,
                default => PaymentStatus::missingPayment,
            };

            if ($competitor = Competitor::where(['financial_book_id' => $payment->book->id])->first()) {
                $competitor->update(['payment_status' => $status]);
            }
        }
    }
}

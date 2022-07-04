<?php

namespace App\Jobs;

use App\Models\Competitor;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessRefund implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(private Competitor $competitor)
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if ($this->competitor->finances->balance->amount < 0) {
            return;
        }

        if ($this->competitor->finances->balance->amount == 0) {
            return;
        }

        $toRefund = $this->competitor->finances->balance->amount;
        foreach ($this->competitor->finances->payments()->paid()->get() as $payment) {
            $refundAmount = min($toRefund, $payment->total->amount);
            Log::alert("competitor has made a payment({$payment->id})! and we want to refund: {$refundAmount}");

            $payment->refund($refundAmount);

            $toRefund -= $refundAmount;
            if ($toRefund == 0) {
                break;
            }
        }
    }
}

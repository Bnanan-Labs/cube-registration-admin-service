<?php

namespace App\Jobs;

use App\Enums\FinancialEntryType;
use App\Enums\PaymentStatus;
use App\Models\Competitor;
use App\Models\Event;
use App\Services\Finances\MoneyBag;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CreateCompetitorBook implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(public Competitor $competitor)
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
        $competition = $this->competitor->competition;
        $events = $this->competitor->events;
        $book = $this->competitor->finances;
        $book->reset();

        // Registration Base Fee
        $book->entries()->create([
            'type' => FinancialEntryType::baseFee,
            'title' => 'Registration Base Fee',
            'balance' => new MoneyBag(-$competition->base_fee->amount),
            'booked_at' => now(),
        ]);

        // Event Fees
        $events->each(function (Event $event) use ($book) {
            if (!$event->fee?->amount) {return;}
            $book->entries()->create([
                'type' => FinancialEntryType::eventFee,
                'title' => $event->title,
                'booked_at' => now(),
                'balance' => new MoneyBag(-$event->fee->amount),
            ]);
        });

        // Waive competitor fee if needed
        if ($this->competitor->shouldWaiveFee) {
            $book->refresh();
            $book->entries()->create([
                'type' => FinancialEntryType::discount,
                'title' => "Staff Discount",
                'booked_at' => now(),
                'balance' => new MoneyBag(-$book->total->amount)
            ]);
        }

        // Guest Fees
        if ($this->competitor->numberOfGuests > 0 && $competition->guest_fee?->amount) {
            $book->entries()->create([
                'type' => FinancialEntryType::guestFee,
                'title' => "{$this->competitor->numberOfGuests} x Guest(s)",
                'booked_at' => now(),
                'balance' => new MoneyBag(-$this->competitor->numberOfGuests * $competition->guest_fee->amount)
            ]);
        }

        // Update payment status
        if ($book->paid->amount > 0) {
            $status = match ($book->balance->amount <=> 0) {
                -1 => PaymentStatus::partiallyPaid,
                0 => PaymentStatus::paid,
                1 => PaymentStatus::needsPartialRefund,
                default => PaymentStatus::missingPayment,
            };
        } else {
            $status = $book->balance->amount === 0 ? PaymentStatus::paid : PaymentStatus::missingPayment;
        }

        $this->competitor->update(['payment_status' => $status]);
    }
}

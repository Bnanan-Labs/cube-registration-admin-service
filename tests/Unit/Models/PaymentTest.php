<?php

namespace Tests\Unit\Models;

use App\Enums\FinancialEntryType;
use App\Enums\PaymentStatus;
use App\Models\Competition;
use App\Models\Competitor;
use App\Models\FinancialBook;
use App\Models\FinancialEntry;
use App\Models\Payment;
use App\Services\Finances\MoneyBag;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class PaymentTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function testCanMarkCompetitorAsPaid(): void
    {
        $amount = 4000;
        $competitor = Competitor::factory()->create(['payment_status' => PaymentStatus::missingPayment]);
        $book = $competitor->finances;
        $book->entries()->create([
            'balance' => new MoneyBag(-$amount),
            'type' => FinancialEntryType::baseFee,
        ]);
        $payment = Payment::factory()->create(['financial_book_id' => $book->id, 'total' => new MoneyBag($amount)]);

        $this->assertEquals(-$amount, $book->balance->amount);
        $this->assertEquals(PaymentStatus::missingPayment, $competitor->payment_status);

        $payment->markAsPaid();

        $book = FinancialBook::find($book->id); // refresh cache
        $competitor = Competitor::find($competitor->id); // refresh cache

        $this->assertEquals(0, $book->balance->amount);
        $this->assertEquals(PaymentStatus::paid, $competitor->payment_status);
    }

    public function testCanMarkCompetitorAsPartiallyPaid(): void
    {
        $amount = 4000;
        $delta = 5;
        $competitor = Competitor::factory()->create(['payment_status' => PaymentStatus::missingPayment]);
        $book = $competitor->finances;
        $book->entries()->create([
            'balance' => new MoneyBag(-$amount),
            'type' => FinancialEntryType::baseFee,
        ]);
        $payment = Payment::factory()->create(['financial_book_id' => $book->id, 'total' => new MoneyBag($amount - $delta)]);

        $this->assertEquals(-$amount, $book->balance->amount);
        $this->assertEquals(PaymentStatus::missingPayment, $competitor->payment_status);

        $payment->markAsPaid();

        $book = FinancialBook::find($book->id); // refresh cache
        $competitor = Competitor::find($competitor->id); // refresh cache

        $this->assertEquals(-$delta, $book->balance->amount);
        $this->assertEquals(PaymentStatus::partiallyPaid, $competitor->payment_status);
    }

    public function testCanMarkCompetitorAsOverlyPaid(): void
    {
        $amount = 4000;
        $delta = 5;
        $competitor = Competitor::factory()->create(['payment_status' => PaymentStatus::missingPayment]);
        $book = $competitor->finances;
        $book->entries()->create([
            'balance' => new MoneyBag(-$amount),
            'type' => FinancialEntryType::baseFee,
        ]);
        $payment = Payment::factory()->create(['financial_book_id' => $book->id, 'total' => new MoneyBag($amount + $delta)]);

        $this->assertEquals(-$amount, $book->balance->amount);
        $this->assertEquals(PaymentStatus::missingPayment, $competitor->payment_status);

        $payment->markAsPaid();

        $book = FinancialBook::find($book->id); // refresh cache
        $competitor = Competitor::find($competitor->id); // refresh cache

        $this->assertEquals($delta, $book->balance->amount);
        $this->assertEquals(PaymentStatus::needsPartialRefund, $competitor->payment_status);
    }
}

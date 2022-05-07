<?php

namespace Services\Finances\Finances;

use App\Enums\PaymentStatus;
use App\Models\Competitor;
use App\Models\Payment;
use App\Services\Finances\MoneyBag;
use App\Services\Finances\PaymentService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaymentServiceTest extends TestCase
{
    use RefreshDatabase;

    public function testCanHandlePaymentService()
    {
        $competitor = Competitor::factory()->create(['payment_status' => PaymentStatus::missingPayment->value]);
        $payment = $competitor->finances->payments()->create([
            'intent_id' => 'test',
            'intent_secret' => 'test',
            'total' => new MoneyBag(500),
            'extra' => 'Test Payment'
        ]);
        $paymentService = new PaymentService();

        $this->assertEquals(PaymentStatus::missingPayment, $competitor->payment_status);
        $this->assertEquals(null, $payment->captured_at);

        $paymentService->handle($payment, 'succeeded');
        $competitor->refresh();

        $this->assertEquals(PaymentStatus::needsPartialRefund, $competitor->payment_status);
        $this->assertNotNull($payment->captured_at);

        $paymentService->handle($payment, 'succeeded');
        $competitor->refresh();

        $this->assertEquals(PaymentStatus::needsPartialRefund, $competitor->payment_status);
        $this->assertNotNull($payment->captured_at);

        $paymentService->handle($payment, 'payment_failed');
        $competitor->refresh();

        $this->assertEquals(PaymentStatus::needsPartialRefund, $competitor->payment_status);
        $this->assertDatabaseMissing(Payment::class, [
            'id' => $payment->id,
        ]);


    }
}

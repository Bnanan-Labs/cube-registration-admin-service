<?php

namespace Functions;

use App\Enums\FinancialEntryType;
use App\Enums\PaymentStatus;
use App\Enums\RegistrationStatus;
use App\Models\Competition;
use App\Models\Competitor;
use App\Models\FinancialBook;
use App\Models\FinancialEntry;
use App\Services\Finances\MoneyBag;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class WaitingListTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function testCanEnforceWaitingList(): void
    {
        $competition = Competition::factory()->create(['competitor_limit' => 3]);
        $competitor1 = Competitor::factory()->create(['registration_status' => RegistrationStatus::pending, 'payment_status' => PaymentStatus::missingPayment]);
        $competitor2 = Competitor::factory()->create(['registration_status' => RegistrationStatus::pending, 'payment_status' => PaymentStatus::missingPayment]);
        $competitor3 = Competitor::factory()->create(['registration_status' => RegistrationStatus::pending, 'payment_status' => PaymentStatus::missingPayment]);
        $competitor4 = Competitor::factory()->create(['registration_status' => RegistrationStatus::pending, 'payment_status' => PaymentStatus::missingPayment]);
        $competitor5 = Competitor::factory()->create(['registration_status' => RegistrationStatus::pending, 'payment_status' => PaymentStatus::missingPayment]);

        $competition->competitors()->saveMany([$competitor1, $competitor2, $competitor3, $competitor4, $competitor5]);

        $competitor1->update(['approved_at' => now()]);
        $this->assertEquals(1, $competition->numberOfApprovedCompetitors);
        $competitor2->update(['approved_at' => now()->addSeconds(1)]);
        $this->assertEquals(2, $competition->numberOfApprovedCompetitors);
        $competitor3->update(['approved_at' => now()->addSeconds(2)]);
        $this->assertEquals(3, $competition->numberOfApprovedCompetitors);
        $competitor4->update(['approved_at' => now()->addSeconds(3)]);
        $this->assertEquals(4, $competition->numberOfApprovedCompetitors);
        $competitor5->update(['approved_at' => now()->addSeconds(4)]);
        $this->assertEquals(5, $competition->numberOfApprovedCompetitors);

        $this->assertEquals(RegistrationStatus::accepted, $competitor1->registration_status);
        $this->assertEquals(RegistrationStatus::accepted, $competitor2->registration_status);
        $this->assertEquals(RegistrationStatus::accepted, $competitor3->registration_status);
        $this->assertEquals(RegistrationStatus::waitingList, $competitor4->registration_status);
        $this->assertEquals(RegistrationStatus::waitingList, $competitor5->registration_status);

        $competitor1->update(['approved_at' => null]);

        $competitor1->refresh();
        $competitor2->refresh();
        $competitor3->refresh();
        $competitor4->refresh();
        $competitor5->refresh();

        $this->assertEquals(RegistrationStatus::pending, $competitor1->registration_status);
        $this->assertEquals(RegistrationStatus::accepted, $competitor2->registration_status);
        $this->assertEquals(RegistrationStatus::accepted, $competitor3->registration_status);
        $this->assertEquals(RegistrationStatus::accepted, $competitor4->registration_status);
        $this->assertEquals(RegistrationStatus::waitingList, $competitor5->registration_status);

        $competitor1->update(['approved_at' => now()->addSeconds(5)]);
        $competitor3->update(['approved_at' => null]);

        $competitor1->refresh();
        $competitor2->refresh();
        $competitor3->refresh();
        $competitor4->refresh();
        $competitor5->refresh();

        $this->assertEquals(RegistrationStatus::waitingList, $competitor1->registration_status);
        $this->assertEquals(RegistrationStatus::accepted, $competitor2->registration_status);
        $this->assertEquals(RegistrationStatus::pending, $competitor3->registration_status);
        $this->assertEquals(RegistrationStatus::accepted, $competitor4->registration_status);
        $this->assertEquals(RegistrationStatus::accepted, $competitor5->registration_status);
    }

    public function testCanCountWaitingList(): void
    {
        $competition = Competition::factory()->create(['competitor_limit' => 2]);
        $competitor1 = Competitor::factory()->create(['registration_status' => RegistrationStatus::pending, 'payment_status' => PaymentStatus::missingPayment]);
        $competitor2 = Competitor::factory()->create(['registration_status' => RegistrationStatus::pending, 'payment_status' => PaymentStatus::missingPayment]);
        $competitor3 = Competitor::factory()->create(['registration_status' => RegistrationStatus::pending, 'payment_status' => PaymentStatus::missingPayment]);
        $competitor4 = Competitor::factory()->create(['registration_status' => RegistrationStatus::pending, 'payment_status' => PaymentStatus::missingPayment]);
        $competitor5 = Competitor::factory()->create(['registration_status' => RegistrationStatus::pending, 'payment_status' => PaymentStatus::missingPayment]);

        $competition->competitors()->saveMany([$competitor1, $competitor2, $competitor3, $competitor4, $competitor5]);

        $competitor1->update(['approved_at' => now()]);
        $competitor2->update(['approved_at' => now()->addSeconds(1)]);
        $competitor3->update(['approved_at' => now()->addSeconds(2)]);
        $competitor4->update(['approved_at' => now()->addSeconds(3)]);
        $competitor5->update(['approved_at' => now()->addSeconds(4)]);

        $this->assertEquals(0, $competitor1->queueNumberInWaitingList);
        $this->assertEquals(0, $competitor2->queueNumberInWaitingList);
        $this->assertEquals(1, $competitor3->queueNumberInWaitingList);
        $this->assertEquals(2, $competitor4->queueNumberInWaitingList);
        $this->assertEquals(3, $competitor5->queueNumberInWaitingList);
    }
}

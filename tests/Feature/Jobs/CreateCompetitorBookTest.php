<?php

namespace Tests\Feature\Jobs;

use App\Enums\FinancialEntryType;
use App\Jobs\CreateCompetitorBook;
use App\Models\Competition;
use App\Models\Competitor;
use App\Models\Event;
use App\Models\Staff;
use App\Services\Finances\MoneyBag;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CreateCompetitorBookTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function testCanCreateCompetitorBook()
    {
        $competition = Competition::factory()->create();
        $events = Event::factory()->times(5)->create();
        $competitor = Competitor::factory()->create(['guests' => ['ano1', 'ano2', 'ano3']]);
        $competitor->events()->saveMany($events);
        $competition->base_fee = new MoneyBag(150);

        // Assert book is empty
        $this->assertEquals(0, $competitor->finances->balance->amount);

        // dispatchSync job
        CreateCompetitorBook::dispatchSync($competitor);

        // Assert side effect happens
        $competitor->load('finances');
        $this->assertEquals(5, $competitor->events()->count());
        $this->assertEquals(1, $competitor->finances->entries()->where('type', FinancialEntryType::baseFee)->count());
        $this->assertEquals(5, $competitor->finances->entries()->where('type', FinancialEntryType::eventFee)->count());
        $this->assertEquals(1, $competitor->finances->entries()->where('type', FinancialEntryType::guestFee)->count());
        $this->assertEquals(0, $competitor->finances->entries()->where('type', FinancialEntryType::discount)->count());
        foreach ($competitor->finances->entries as $entry) {
            $this->assertLessThan(0, $entry->balance->amount, "Entry of type '{$entry->type->value}' was expected to be a negative value");
        }
    }

    public function testCanRecreateCompetitorBookWithZeroEvents()
    {
        $competition = Competition::factory()->create();
        $events = Event::factory()->times(5)->create();
        $competitor = Competitor::factory()->create(['guests' => ['ano1', 'ano2', 'ano3']]);
        $competitor->events()->saveMany($events);
        $competition->base_fee = new MoneyBag(150);

        // dispatchSync job
        CreateCompetitorBook::dispatchSync($competitor);
        $this->assertEquals(5, $competitor->events()->count());

        // Assert side effect happens
        $competitor->events()->delete();

        // dispatchSync job again now with 0 events
        CreateCompetitorBook::dispatchSync($competitor);
        $this->assertEquals(0, $competitor->events()->count());

        // Assert side effect happens
        $competitor->load('finances');
        $this->assertEquals(1, $competitor->finances->entries()->where('type', FinancialEntryType::baseFee)->count());
        $this->assertEquals(0, $competitor->finances->entries()->where('type', FinancialEntryType::eventFee)->count());
        $this->assertEquals(1, $competitor->finances->entries()->where('type', FinancialEntryType::guestFee)->count());
        $this->assertEquals(0, $competitor->finances->entries()->where('type', FinancialEntryType::discount)->count());
    }

    public function testCanRecreateCompetitorBookWithMoreEvents()
    {
        $competition = Competition::factory()->create();
        $events = Event::factory()->times(5)->create();
        $competitor = Competitor::factory()->create(['guests' => ['ano1', 'ano2', 'ano3']]);
        $competition->base_fee = new MoneyBag(150);

        // dispatchSync job
        CreateCompetitorBook::dispatchSync($competitor);
        $this->assertEquals(0, $competitor->events()->count());

        // Assert side effect happens
        $competitor->events()->saveMany($events);

        // dispatchSync job again now with 0 events
        CreateCompetitorBook::dispatchSync($competitor);
        $this->assertEquals(5, $competitor->events()->count());

        // Assert side effect happens
        $competitor->load('finances');
        $this->assertEquals(1, $competitor->finances->entries()->where('type', FinancialEntryType::baseFee)->count());
        $this->assertEquals(5, $competitor->finances->entries()->where('type', FinancialEntryType::eventFee)->count());
        $this->assertEquals(1, $competitor->finances->entries()->where('type', FinancialEntryType::guestFee)->count());
        $this->assertEquals(0, $competitor->finances->entries()->where('type', FinancialEntryType::discount)->count());
    }

    public function testCanApplyDiscountToStaffCompetitorBooks()
    {
        $competition = Competition::factory()->create();
        $events = Event::factory()->times(5)->create();
        $competitor = Competitor::factory()->create(['guests' => [], 'competition_id' => $competition->id]); // no guests
        $competitor->events()->saveMany($events);
        Staff::factory()->create(['wca_id' => $competitor->wca_id, 'competition_id' => $competition->id]);
        $competition->base_fee = new MoneyBag(150);

        // dispatchSync job
        CreateCompetitorBook::dispatchSync($competitor);
        $this->assertEquals(5, $competitor->events()->count());

        // Assert side effect happens
        $competitor->events()->saveMany($events);

        // Assert side effect happens
        $competitor->load('finances');
        $this->assertEquals(1, $competitor->finances->entries()->where('type', FinancialEntryType::baseFee)->count());
        $this->assertEquals(5, $competitor->finances->entries()->where('type', FinancialEntryType::eventFee)->count());
        $this->assertEquals(0, $competitor->finances->entries()->where('type', FinancialEntryType::guestFee)->count());
        $this->assertEquals(1, $competitor->finances->entries()->where('type', FinancialEntryType::discount)->count());
        $this->assertEquals(0, $competitor->finances->balance->amount);
    }

    public function testCanApplyDiscountPaymentExemptCompetitorBooks()
    {
        $competition = Competition::factory()->create();
        $events = Event::factory()->times(5)->create();
        $competitor = Competitor::factory()->create(['guests' => [], 'competition_id' => $competition->id, 'is_exempt_from_payment' => true]); // no guests
        $competitor->events()->saveMany($events);
        $competition->base_fee = new MoneyBag(150);

        // dispatchSync job
        CreateCompetitorBook::dispatchSync($competitor);
        $this->assertEquals(5, $competitor->events()->count());

        // Assert side effect happens
        $competitor->events()->saveMany($events);

        // Assert side effect happens
        $competitor->load('finances');
        $this->assertEquals(1, $competitor->finances->entries()->where('type', FinancialEntryType::baseFee)->count());
        $this->assertEquals(5, $competitor->finances->entries()->where('type', FinancialEntryType::eventFee)->count());
        $this->assertEquals(0, $competitor->finances->entries()->where('type', FinancialEntryType::guestFee)->count());
        $this->assertEquals(1, $competitor->finances->entries()->where('type', FinancialEntryType::discount)->count());
        $this->assertEquals(0, $competitor->finances->balance->amount);
    }

    public function testCanPreservePaymentEntriesWhenRecreatingCompetitorBooks()
    {
        $competition = Competition::factory()->create();
        $events = Event::factory()->times(5)->create();
        $competitor = Competitor::factory()->create(['guests' => ['ano1', 'ano2', 'ano3']]);
        $competitor->events()->saveMany($events);
        $competition->base_fee = new MoneyBag(150);

        // dispatchSync job
        CreateCompetitorBook::dispatchSync($competitor);
        $this->assertEquals(5, $competitor->events()->count());

        // make a payment
        $competitor->finances->entries()->create([
            'type' => FinancialEntryType::payment,
            'title' => 'Payment',
            'booked_at' => now(),
            'balance' => new MoneyBag(200)
        ]);
        $this->assertEquals(1, $competitor->finances->entries()->where('type', FinancialEntryType::payment)->count());

        // dispatchSync job again now with 0 events
        CreateCompetitorBook::dispatchSync($competitor);
        $this->assertEquals(5, $competitor->events()->count());

        // Assert side effect happens
        $competitor->load('finances');
        $this->assertEquals(1, $competitor->finances->entries()->where('type', FinancialEntryType::baseFee)->count());
        $this->assertEquals(5, $competitor->finances->entries()->where('type', FinancialEntryType::eventFee)->count());
        $this->assertEquals(1, $competitor->finances->entries()->where('type', FinancialEntryType::guestFee)->count());
        $this->assertEquals(0, $competitor->finances->entries()->where('type', FinancialEntryType::discount)->count());
        $this->assertEquals(1, $competitor->finances->entries()->where('type', FinancialEntryType::payment)->count());
    }
}

<?php

namespace Tests\Feature\Jobs;

use App\Enums\FinancialEntryType;
use App\Jobs\CreateCompetitorBook;
use App\Models\Competition;
use App\Models\Competitor;
use App\Models\Event;
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
        foreach ($competitor->finances->entries as $entry) {
            $this->assertLessThan(0, $entry->balance->amount, "Entry of type '{$entry->type->value}' was expected to be a negative value");
        }
    }
}

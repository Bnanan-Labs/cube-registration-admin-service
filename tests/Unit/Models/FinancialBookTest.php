<?php

namespace Models;

use App\Enums\FinancialEntryType;
use App\Models\FinancialBook;
use App\Models\FinancialEntry;
use App\Services\Finances\MoneyBag;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class FinancialBookTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function testCanSortEntries(): void
    {
        $book = FinancialBook::factory()->create();
        $entry1 = FinancialEntry::factory()->create(['booked_at' => Carbon::now()->subDays(3)]);
        $entry2 = FinancialEntry::factory()->create(['booked_at' => Carbon::now()->subDays(2)]);
        $entry3 = FinancialEntry::factory()->create(['booked_at' => Carbon::now()->subDays(1)]);
        $entry4 = FinancialEntry::factory()->create(['booked_at' => Carbon::now()]);

        $book->entries()->saveMany([$entry4, $entry2, $entry1, $entry3]);

        $this->assertEquals($entry1->id, $book->entries[0]->id);
        $this->assertEquals($entry2->id, $book->entries[1]->id);
        $this->assertEquals($entry3->id, $book->entries[2]->id);
        $this->assertEquals($entry4->id, $book->entries[3]->id);
    }

    public function testSummationOfBookBalance(): void
    {
        $book = FinancialBook::factory()->create();
        $book->entries()->create([
            'type' => FinancialEntryType::payment,
            'balance' => new MoneyBag(200),
            'title' => 'Just a random payment',
        ]);
        $book->entries()->create([
            'type' => FinancialEntryType::baseFee,
            'balance' => new MoneyBag(-200),
            'title' => 'Base Fee',
        ]);
        $book->entries()->create([
            'type' => FinancialEntryType::eventFee,
            'balance' => new MoneyBag(-20),
            'title' => 'Event fee',
        ]);
        $book->entries()->create([
            'type' => FinancialEntryType::eventFee,
            'balance' => new MoneyBag(-20),
            'title' => 'Event fee',
        ]);
        $book->entries()->create([
            'type' => FinancialEntryType::eventFee,
            'balance' => new MoneyBag(-20),
            'title' => 'Event fee',
        ]);
        $book->entries()->create([
            'type' => FinancialEntryType::eventFee,
            'balance' => new MoneyBag(-20),
            'title' => 'Event fee',
        ]);
        $book->entries()->create([
            'type' => FinancialEntryType::eventFee,
            'balance' => new MoneyBag(-20),
            'title' => 'Event fee',
        ]);
        $book->entries()->create([
            'type' => FinancialEntryType::guestFee,
            'balance' => new MoneyBag(-35),
            'title' => 'Guest fee',
        ]);
        $book->entries()->create([
            'type' => FinancialEntryType::refund,
            'balance' => new MoneyBag(-30),
            'title' => 'Refund',
        ]);
        $book->entries()->create([
            'type' => FinancialEntryType::discount,
            'balance' => new MoneyBag(27),
            'title' => 'Discount',
        ]);

        $this->assertEquals(-308, $book->total->amount);
        $this->assertEquals(200, $book->paid->amount);
        $this->assertEquals(-30, $book->refunded->amount);
        $this->assertEquals(-138, $book->balance->amount);
    }
}

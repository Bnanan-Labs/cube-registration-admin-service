<?php

namespace Services;

use App\Services\Finances\MoneyBag;
use Tests\TestCase;

class MoneyBagTest extends TestCase
{
    public function testCanAdd()
    {
        $a = new MoneyBag(1);
        $b = new MoneyBag(2);

        $a->add($b); // a = 1 + 2 = 3
        $b->add($a); // b = 2 + 3 = 5

        $this->assertEquals(3, $a->amount);
        $this->assertEquals(5, $b->amount);
    }

    public function testThrowsExceptionIfMixedCurrencies()
    {
        $this->expectException(\Exception::class);

        $a = new MoneyBag(1, 'DKK');
        $b = new MoneyBag(2, 'XXX');

        $a->add($b);
    }
}

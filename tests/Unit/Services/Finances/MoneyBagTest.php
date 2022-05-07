<?php

namespace Services\Finances\Finances;

use App\Services\Finances\Casts\MoneyBagCast;
use App\Services\Finances\MoneyBag;
use Tests\TestCase;

class MoneyBagTest extends TestCase
{
    public function testCanAddMoneyBags()
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

    public function testCanCastNullValues()
    {
        $model = null; // we don't actually need to test against a model
        $caster = new MoneyBagCast();

        $this->assertEquals(null, $caster->get($model, '', null, []));
        $this->assertEquals(null, $caster->set($model, '', null, []));
    }

    public function testCanCastValues()
    {
        $model = null; // we don't actually need to test against a model
        $caster = new MoneyBagCast();

        $this->assertEquals(2, $caster->get($model, '', 2, [])?->amount);
        $this->assertEquals(2, $caster->set($model, '', new MoneyBag(2, 'DKK'), []));
    }
}

<?php

namespace App\Services\Finances;

use App\Services\Finances\Casts\MoneyBagCast;

class MoneyBag
{
    public function __construct(public int $amount = 0, public string $currency = 'EUR')
    {
        //
    }

    public function add(MoneyBag $b): MoneyBag
    {
        if ($this->currency !== $b->currency) {
            throw new \Exception('You cannot add 2 monetary values together if they occur in separate currencies');
        }

        $this->amount += $b->amount;
        return $this;
    }
}

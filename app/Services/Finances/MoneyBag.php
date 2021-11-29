<?php

namespace App\Services\Finances;

class MoneyBag
{
    public function __construct(public int $amount = 0, public string $currency = 'EUR')
    {
        //
    }
}

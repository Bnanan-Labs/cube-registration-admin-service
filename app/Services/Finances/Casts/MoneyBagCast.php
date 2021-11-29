<?php

namespace App\Services\Finances\Casts;

use App\Services\Finances\MoneyBag;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;

class MoneyBagCast implements CastsAttributes
{

    public function get($model, string $key, $value, array $attributes)
    {
        return new MoneyBag(amount: $value);
    }

    public function set($model, string $key, $value, array $attributes)
    {
        return $value->amount;
    }
}

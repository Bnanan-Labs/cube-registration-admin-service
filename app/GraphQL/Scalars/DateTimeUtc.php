<?php

namespace App\GraphQl\Scalars;

use Illuminate\Support\Carbon;
use Nuwave\Lighthouse\Schema\Types\Scalars\DateScalar;

/**
 * Only works with Carbon 2.
 */
class DateTimeUtc extends DateScalar
{
    protected function format(Carbon $carbon): string
    {
        return $carbon->toISOString();
    }

    protected function parse($value): Carbon
    {
        return Carbon::createFromIsoFormat('YYYY-MM-DDTHH:mm:ss.SSSSSSZ', $value);
    }
}

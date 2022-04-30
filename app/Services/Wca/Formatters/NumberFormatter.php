<?php

namespace App\Services\Wca\Formatters;

class NumberFormatter extends Formatter
{
    /**
     * @param int $result
     * @return string
     */
    public function toString(int $result): string
    {
        return (string) ($result / 100);
    }

    /**
     * @param string $result
     * @return int
     */
    public function toValue(string $result): int
    {
        return (int) ((float) $result) * 100;
    }
}

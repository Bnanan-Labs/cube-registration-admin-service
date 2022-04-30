<?php

namespace App\Services\Wca\Formatters;

abstract class Formatter
{
    /**
     * @param int $result
     * @return string
     */
    public function toString(int $result): string
    {
        return (string) $result;
    }

    /**
     * @param string $result
     * @return int
     */
    public function toValue(string $result): int
    {
        return (int) $result;
    }
}

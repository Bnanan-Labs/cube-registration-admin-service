<?php

namespace App\Services\Wca\Formatters;

use Illuminate\Support\Str;

class TimeFormatter extends Formatter
{
    /**
     * @param int $result
     * @return string
     */
    public function toString(int $result): string
    {
        $hours = floor($result / 360000);
        $mins = floor($result / 6000) % 60;
        $seconds = floor($result / 100) % 60;
        $centiseconds = $result % 100;

        if ($hours) {
            $mins = $mins >= 10 ? $mins : "0" . $mins;
            $seconds = $seconds >= 10 ? $seconds : "0" . $seconds;
            $centiseconds = $centiseconds >= 10 ? $centiseconds : "0" . $centiseconds;

            return "{$hours}:{$mins}:{$seconds}.{$centiseconds}";
        }

        if ($mins) {
            $seconds = $seconds >= 10 ? $seconds : "0" . $seconds;
            $centiseconds = $centiseconds >= 10 ? $centiseconds : "0" . $centiseconds;

            return "{$mins}:{$seconds}.{$centiseconds}";
        }

        $centiseconds = $centiseconds >= 10 ? $centiseconds : "0" . $centiseconds;
        return "{$seconds}.{$centiseconds}";
    }

    /**
     * @param string $result
     * @return int
     */
    public function toValue(string $result): int
    {
        $parts = collect(explode(':', $result))->pad(-3, 0);
        return (int) (((int) $parts[0]) * 360000 + ((int) $parts[1]) * 6000 + ((float) $parts[2]) * 100);
    }
}

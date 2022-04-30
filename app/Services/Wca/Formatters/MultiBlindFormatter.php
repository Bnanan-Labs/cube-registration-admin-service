<?php

namespace App\Services\Wca\Formatters;

class MultiBlindFormatter extends Formatter
{
    /**
     * @param int $result
     * @return string
     */
    public function toString(int $result): string
    {
        $points = 99 - floor($result / 10000000);
        $timeInSeconds = floor($result / 100) % 100000;
        $unsolved = $result % 100;
        $solved = $points + $unsolved;
        $attempted = $points + $unsolved * 2;

        $hours = floor($timeInSeconds / 3600);
        $mins = floor($timeInSeconds / 60) % 60;
        $seconds = ($timeInSeconds % 60);

        if ($hours) {
            $mins = $mins >= 10 ? $mins : "0" . $mins;
            $seconds = $seconds >= 10 ? $seconds : "0" . $seconds;
            $time = "{$hours}:{$mins}:{$seconds}";
        } elseif ($mins) {
            $seconds = $seconds >= 10 ? $seconds : "0" . $seconds;
            $time =  "{$mins}:{$seconds}";
        } else {
            $time = $timeInSeconds;
        }

        return "{$solved}/{$attempted} {$time}";
    }

    /**
     * @param string $result
     * @return int
     */
    public function toValue(string $result): int
    {
        $parts = [];
        preg_match('/(\d+)\/(\d+) (.+)/', $result, $parts);
        [$solved, $attempted, $time] = $parts;

        $unsolved = $attempted - $solved;
        $score = $solved - $unsolved;
        $parts = collect(explode(':', $result))->pad(-3, 0);
        $timeInSeconds = ((int) $parts[0]) * 3600 + ((int) $parts[1]) * 60 + ((int) $parts[2]);
        return $score * 10000000 + $timeInSeconds * 100 + $unsolved;
    }
}

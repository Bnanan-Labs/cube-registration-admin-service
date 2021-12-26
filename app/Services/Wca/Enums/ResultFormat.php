<?php

namespace App\Services\Wca\Enums;

enum ResultFormat: string
{
    case averageOfFive = 'avg5';
    case meanOfThree = 'mo3';
    case bestOfThree = 'bo3';
    case bestOfTwo = 'bo2';
    case bestOfOne = 'bo1';
}

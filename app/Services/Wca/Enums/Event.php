<?php

namespace App\Services\Wca\Enums;

enum Event: string
{
    case twoByTwo = '222';
    case threeByThree = '333';
    case fourByFour = '444';
    case fiveByFive = '555';
    case sixBySix = '666';
    case sevenBySeven = '777';
    case squareOne = 'sq1';
    case clock = 'clock';
    case pyraminx = 'pyram';
    case megaminx = 'minx';
    case skewb = 'skewb';
    case threeOneHanded = '3oh';
    case fewestMoves = 'fmc';
    case threeBlind = '3bld';
    case fourBlind = '4bld';
    case fiveBlind = '5bld';
    case multiBlind = 'mbld';
}

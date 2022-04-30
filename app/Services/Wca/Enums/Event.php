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
    case threeOneHanded = '333oh';
    case fewestMoves = '333fm';
    case threeBlind = '333bf';
    case fourBlind = '444bf';
    case fiveBlind = '555bf';
    case multiBlind = '333mbf';
}

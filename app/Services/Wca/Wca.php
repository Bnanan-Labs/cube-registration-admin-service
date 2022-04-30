<?php

namespace App\Services\Wca;

use App\Services\Wca\Enums\Event;
use App\Services\Wca\Enums\ResultFormat;
use App\Services\Wca\Events\WcaEvent;
use GuzzleHttp\Client;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;

class Wca
{
    private Collection $events;

    public function __construct()
    {
        $this->initialiseEvents();
    }

    private function initialiseEvents()
    {
        $this->events = collect([
            new WcaEvent(id: '222', fullName: '2x2x2', shortName: '2x2x2', resultFormat: ResultFormat::averageOfFive, event: Event::twoByTwo),
            new WcaEvent(id: '333', fullName: '3x3x3', shortName: '3x3x3', resultFormat: ResultFormat::averageOfFive, event: Event::threeByThree),
            new WcaEvent(id: '444', fullName: '4x4x4', shortName: '4x4x4', resultFormat: ResultFormat::averageOfFive, event: Event::fourByFour),
            new WcaEvent(id: '555', fullName: '5x5x5', shortName: '5x5x5', resultFormat: ResultFormat::averageOfFive, event: Event::fiveByFive),
            new WcaEvent(id: '666', fullName: '6x6x6', shortName: '6x6x6', resultFormat: ResultFormat::averageOfFive, event: Event::sixBySix),
            new WcaEvent(id: '777', fullName: '7x7x7', shortName: '7x7x7', resultFormat: ResultFormat::averageOfFive, event: Event::sevenBySeven),
            new WcaEvent(id: '333oh', fullName: '3x3x3 One-handed', shortName: '3x3x3 One-handed', resultFormat: ResultFormat::averageOfFive, event: Event::threeOneHanded),
            new WcaEvent(id: '333bf', fullName: '3x3x3 Blindfolded', shortName: '3x3x3 Blindfolded', resultFormat: ResultFormat::bestOfThree, event: Event::threeBlind),
            new WcaEvent(id: '333fm', fullName: 'Fewest Moves', shortName: 'Fewest Moves', resultFormat: ResultFormat::meanOfThree, event: Event::fewestMoves),
            new WcaEvent(id: 'sq1', fullName: 'Square-1', shortName: 'Square-1', resultFormat: ResultFormat::averageOfFive, event: Event::squareOne),
            new WcaEvent(id: 'clock', fullName: 'Clock', shortName: 'Clock', resultFormat: ResultFormat::averageOfFive, event: Event::clock),
            new WcaEvent(id: 'pyram', fullName: 'Pyraminx', shortName: 'Pyraminx', resultFormat: ResultFormat::averageOfFive, event: Event::pyraminx),
            new WcaEvent(id: 'minx', fullName: 'Megaminx', shortName: 'Megaminx', resultFormat: ResultFormat::averageOfFive, event: Event::megaminx),
            new WcaEvent(id: 'skewb', fullName: 'Skewb', shortName: 'Skewb', resultFormat: ResultFormat::averageOfFive, event: Event::skewb),
            new WcaEvent(id: '444bf', fullName: '4x4x4 Blindfolded', shortName: '4x4x4 Blindfolded', resultFormat: ResultFormat::bestOfThree, event: Event::fourBlind),
            new WcaEvent(id: '555bf', fullName: '5x5x5 Blindfolded', shortName: '5x5x5 Blindfolded', resultFormat: ResultFormat::bestOfThree, event: Event::fiveBlind),
            new WcaEvent(id: '333mbf', fullName: 'Multi BLD', shortName: 'Multi BLD', resultFormat: ResultFormat::bestOfThree, event: Event::multiBlind),
        ]);
    }

    public function events(): Collection
    {
        return $this->events;
    }

    public function event(Event $enum): WcaEvent
    {
        return $this->events->first(fn (WcaEvent $event) => $event->event === $enum);
    }

    public function getPerson($wcaId)
    {
        return Http::wca()->get("/persons/{$wcaId}")->json();
    }
}

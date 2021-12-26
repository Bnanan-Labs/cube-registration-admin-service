<?php

namespace Tests\Unit\Services;

use App\Services\Wca\Enums\Event;
use App\Services\Wca\Enums\ResultFormat;
use App\Services\Wca\Wca;
use Tests\TestCase;

class WcaTest extends TestCase
{
    protected Wca $wca;

    protected function setUp(): void
    {
        parent::setUp();

        $this->wca = new Wca();
    }

    public function testCanGetEventById()
    {
        $megaminx = $this->wca->event(Event::megaminx);
        $this->assertEquals(
            Event::megaminx,
            $megaminx->event,
        );
        $this->assertEquals(
            ResultFormat::averageOfFive,
            $megaminx->resultFormat,
        );
    }

    public function testCanGetEvenAllEvents()
    {
        $all = $this->wca->events();
        $this->assertEquals(
            17,
            $all->count(),
        );
    }
}

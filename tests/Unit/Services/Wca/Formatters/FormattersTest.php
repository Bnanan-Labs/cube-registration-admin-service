<?php

namespace Tests\Unit\Services\Wca\Formatters;

use App\Services\Wca\Enums\Event;
use App\Services\Wca\Enums\ResultFormat;
use App\Services\Wca\Formatters\MultiBlindFormatter;
use App\Services\Wca\Formatters\NumberFormatter;
use App\Services\Wca\Formatters\TimeFormatter;
use App\Services\Wca\Wca;
use Tests\TestCase;

class FormattersTest extends TestCase
{
    public function testCanNumberFormatterFormat()
    {
        $result = 2200;
        $formattedResult = '22';
        $formatter = new NumberFormatter();

        $this->assertEquals($formattedResult, $formatter->toString($result));
        $this->assertEquals($result, $formatter->toValue($formattedResult));

        $result = 2254;
        $formattedResult = '22.54';

        $this->assertEquals($formattedResult, $formatter->toString($result));
        $this->assertEquals($result, $formatter->toValue($formattedResult));

        $result = 220000;
        $formattedResult = '2200';

        $this->assertEquals($formattedResult, $formatter->toString($result));
        $this->assertEquals($result, $formatter->toValue($formattedResult));
    }

    public function testCanTimeFormatterFormat()
    {
        $result = 2200;
        $formattedResult = '22.00';
        $formatter = new TimeFormatter();

        $this->assertEquals($formattedResult, $formatter->toString($result));
        $this->assertEquals($result, $formatter->toValue($formattedResult));

        $result = 2254;
        $formattedResult = '22.54';

        $this->assertEquals($formattedResult, $formatter->toString($result));
        $this->assertEquals($result, $formatter->toValue($formattedResult));

        $result = 8254;
        $formattedResult = '1:22.54';

        $this->assertEquals($formattedResult, $formatter->toString($result));
        $this->assertEquals($result, $formatter->toValue($formattedResult));

        $result = 370000;
        $formattedResult = '1:01:40.00';

        $this->assertEquals($formattedResult, $formatter->toString($result));
        $this->assertEquals($result, $formatter->toValue($formattedResult));
    }

    public function testCanMultiBlindFormatterFormat()
    {
        $result = 740349700;
        $formattedResult = '25/25 58:17';
        $formatter = new MultiBlindFormatter();

        $this->assertEquals($formattedResult, $formatter->toString($result));
        $this->assertEquals($result, $formatter->toValue($formattedResult));

        $result = 740360200;
        $formattedResult = '25/25 1:00:02';
        $formatter = new MultiBlindFormatter();

        $this->assertEquals($formattedResult, $formatter->toString($result));
        $this->assertEquals($result, $formatter->toValue($formattedResult));

        $result = 740000200;
        $formattedResult = '25/25 2';
        $formatter = new MultiBlindFormatter();

        $this->assertEquals($formattedResult, $formatter->toString($result));
        $this->assertEquals($result, $formatter->toValue($formattedResult));

        $result = 590352702;
        $formattedResult = '42/44 58:47';

        $this->assertEquals($formattedResult, $formatter->toString($result));
        $this->assertEquals($result, $formatter->toValue($formattedResult));
    }
}

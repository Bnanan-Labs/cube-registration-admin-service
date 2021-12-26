<?php

namespace App\Services\Wca\Events;

use App\Services\Wca\Enums\Event;
use App\Services\Wca\Enums\ResultFormat;

class WcaEvent
{
    public function __construct(
        public readonly string $id,
        public readonly string $fullName,
        public readonly string $shortName,
        public readonly ResultFormat $resultFormat,
        public readonly Event $event,
    )
    {
        //
    }
}

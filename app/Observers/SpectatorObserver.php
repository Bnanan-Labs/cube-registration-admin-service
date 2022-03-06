<?php

namespace App\Observers;

use App\Models\Competition;
use App\Models\Spectator;

class SpectatorObserver
{
    public function creating(Spectator $spectator): void
    {
        if (!$spectator->isDirty('competition_id')) {
            $spectator->competition()->associate(Competition::first());
        }
    }
}

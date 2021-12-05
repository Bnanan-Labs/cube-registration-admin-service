<?php

namespace App\Policies;

use App\Models\Spectator;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class SpectatorPolicy
{
    use HandlesAuthorization;

    public function create(User $user): bool
    {
        return (bool) $user->is_manager;
    }

    public function manage(User $user, ?Spectator $spectator = null): bool
    {
        return (bool) $user->is_manager;
    }
}

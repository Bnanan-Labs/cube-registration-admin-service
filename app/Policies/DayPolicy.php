<?php

namespace App\Policies;

use App\Models\Day;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class DayPolicy
{
    use HandlesAuthorization;

    public function manage(User $user, ?Day $day = null): bool
    {
        return (bool) $user->is_manager;
    }
}

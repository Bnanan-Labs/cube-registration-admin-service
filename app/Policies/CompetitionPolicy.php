<?php

namespace App\Policies;

use App\Models\Competition;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class CompetitionPolicy
{
    use HandlesAuthorization;

    public function manage(User $user, Competition $competition): bool
    {
        return (bool) $user->is_manager;
    }
}

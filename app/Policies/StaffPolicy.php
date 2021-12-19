<?php

namespace App\Policies;

use App\Models\Staff;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class StaffPolicy
{
    use HandlesAuthorization;

    public function manage(User $user, ?Staff $staff = null): bool
    {
        return (bool) $user->is_manager;
    }
}

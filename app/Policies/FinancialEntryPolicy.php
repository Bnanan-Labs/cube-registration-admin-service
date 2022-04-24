<?php

namespace App\Policies;

use App\Models\Competitor;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class FinancialEntryPolicy
{
    use HandlesAuthorization;

    public function crud(User $user): bool
    {
        return $user->is_manager;
    }
}

<?php

namespace App\Policies;

use App\Models\Competitor;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Facades\Log;

class CompetitorPolicy
{
    use HandlesAuthorization;

    public function manage(User $user, Competitor $competitor): bool
    {
        $policyResult = $user->is_manager || $competitor->wca_id === $user->wca_id ? 'GRANTED' : 'DENIED';
        $managerStatus = $user->is_manager ? 'Manager' : 'User';
        Log::alert("{$managerStatus} '{$user->name}' #{$user->id} is trying to access competitor '{$competitor->first_name}' #{$competitor->id}. Comparing '{$user->wca_id}' with '{$competitor->wca_id}'. Access will be {$policyResult}.");
        return $user->is_manager || $competitor->wca_id === $user->wca_id;
    }

    public function crud(User $user): bool
    {
        return $user->is_manager;
    }
}

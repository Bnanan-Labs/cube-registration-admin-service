<?php

namespace App\Policies;

use App\Models\Competitor;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class CompetitorPolicy
{
    use HandlesAuthorization;

    public function manage(User $user, Competitor $competitor, array $args = []): bool
    {
        $competitor = isset($args['id']) ? Competitor::find($args['id']) : $competitor;
        dd([$args, $competitor->toArray(), $user->toArray()]);
        return $user->is_manager || $competitor->wca_id === $user->wca_id;
    }

    public function crud(User $user): bool
    {
        return $user->is_manager;
    }
}

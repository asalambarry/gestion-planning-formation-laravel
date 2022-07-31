<?php

namespace App\Policies;

use App\Models\Planning;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class PlanningPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the model.
     *
     * @param User $user
     * @param Planning $planning
     * @return mixed
     */
    public function view(User $user, Planning $planning)
    {
        return ($user->isAdmin() || $user->isEnseignant() || $user->id === $planning->cours->user_id);
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param User $user
     * @param Planning $planning
     * @return mixed
     */
    public function update(User $user, Planning $planning)
    {
        return ($user->isAdmin() || $user->isEnseignant() || $user->id === $planning->cours->user_id);
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param User $user
     * @param Planning $planning
     * @return mixed
     */
    public function delete(User $user, Planning $planning)
    {
        return ($user->isAdmin() || $user->isEnseignant() || $user->id === $planning->cours->user_id);
    }
}

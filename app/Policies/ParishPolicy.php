<?php

namespace App\Policies;

use App\Models\Parish;
use App\Models\User;

class ParishPolicy
{
    /**
     * Determine whether the user can view any parishes.
     */
    public function viewAny(User $user): bool
    {
        return $user->isSuperAdmin() || $user->isChairperson();
    }

    /**
     * Determine whether the user can view the parish.
     */
    public function view(User $user, Parish $parish): bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        return $user->isChairperson() && $user->parish_id === $parish->id;
    }

    /**
     * Determine whether the user can create parishes.
     */
    public function create(User $user): bool
    {
        return $user->isSuperAdmin();
    }

    /**
     * Determine whether the user can update the parish.
     */
    public function update(User $user, Parish $parish): bool
    {
        return $user->isSuperAdmin();
    }

    /**
     * Determine whether the user can delete the parish.
     */
    public function delete(User $user, Parish $parish): bool
    {
        return $user->isSuperAdmin();
    }
}

<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return in_array($user->role, ['super_admin', 'deanery_admin', 'chairperson']);
    }

    /**
     * Determine whether the user can view the model.
     * Enforces that Chairperson can ONLY view youth belonging to their parish.
     */
    public function view(User $user, User $model): bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        if ($user->isChairperson()) {
            return $model->role === 'youth' && $model->parish_id === $user->parish_id;
        }

        return $user->id === $model->id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return in_array($user->role, ['super_admin', 'chairperson']);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, User $model): bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        if ($user->isChairperson()) {
            return $model->role === 'youth' && $model->parish_id === $user->parish_id;
        }

        return $user->id === $model->id;
    }

    /**
     * Determine whether the user can delete/suspend the model.
     */
    public function delete(User $user, User $model): bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        if ($user->isChairperson()) {
            return $model->role === 'youth' && $model->parish_id === $user->parish_id;
        }

        return false;
    }
}

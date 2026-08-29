<?php

namespace App\Policies;

use App\Models\ParishCompetition;
use App\Models\User;

class ParishCompetitionPolicy
{
    public function viewAny(User $user): bool
    {
        return in_array($user->role, ['super_admin', 'chairperson']);
    }

    public function view(User $user, ParishCompetition $competition): bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        return $competition->parish_id === $user->parish_id;
    }

    public function create(User $user): bool
    {
        return in_array($user->role, ['super_admin', 'chairperson']);
    }

    public function update(User $user, ParishCompetition $competition): bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        return $competition->parish_id === $user->parish_id;
    }

    public function delete(User $user, ParishCompetition $competition): bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        return $competition->parish_id === $user->parish_id;
    }
}

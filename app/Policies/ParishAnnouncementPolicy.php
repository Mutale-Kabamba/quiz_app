<?php

namespace App\Policies;

use App\Models\ParishAnnouncement;
use App\Models\User;

class ParishAnnouncementPolicy
{
    public function viewAny(User $user): bool
    {
        return in_array($user->role, ['super_admin', 'chairperson']);
    }

    public function view(User $user, ParishAnnouncement $announcement): bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        return $announcement->parish_id === $user->parish_id;
    }

    public function create(User $user): bool
    {
        return in_array($user->role, ['super_admin', 'chairperson']);
    }

    public function update(User $user, ParishAnnouncement $announcement): bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        return $announcement->parish_id === $user->parish_id;
    }

    public function delete(User $user, ParishAnnouncement $announcement): bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        return $announcement->parish_id === $user->parish_id;
    }
}

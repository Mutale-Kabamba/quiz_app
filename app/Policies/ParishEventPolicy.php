<?php

namespace App\Policies;

use App\Models\ParishEvent;
use App\Models\User;

class ParishEventPolicy
{
    public function viewAny(User $user): bool
    {
        return in_array($user->role, ['super_admin', 'chairperson']);
    }

    public function view(User $user, ParishEvent $event): bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        return $event->parish_id === $user->parish_id;
    }

    public function create(User $user): bool
    {
        return in_array($user->role, ['super_admin', 'chairperson']);
    }

    public function update(User $user, ParishEvent $event): bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        return $event->parish_id === $user->parish_id;
    }

    public function delete(User $user, ParishEvent $event): bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        return $event->parish_id === $user->parish_id;
    }
}

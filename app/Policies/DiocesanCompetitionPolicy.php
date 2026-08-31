<?php

namespace App\Policies;

use App\Models\DiocesanCompetition;
use App\Models\User;

class DiocesanCompetitionPolicy
{
    /**
     * Determine whether the user can view any competitions.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, DiocesanCompetition $competition): bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        if ($competition->isDioceseScope()) {
            return true;
        }

        if ($competition->isDeaneryScope()) {
            return $user->parish?->deanery_id === $competition->deanery_id;
        }

        if ($competition->isParishScope()) {
            return $user->parish_id === $competition->parish_id;
        }

        if ($competition->isCustomScope()) {
            return $competition->participants()->where('user_id', $user->id)->exists();
        }

        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->isSuperAdmin() || $user->isChairperson();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, DiocesanCompetition $competition): bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        if ($user->isChairperson() && $competition->parish_id === $user->parish_id) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, DiocesanCompetition $competition): bool
    {
        return $user->isSuperAdmin();
    }

    /**
     * Determine whether the user can manage participants and join requests.
     */
    public function manageParticipants(User $user, DiocesanCompetition $competition): bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        if ($user->isChairperson() && $competition->parish_id === $user->parish_id) {
            return true;
        }

        return false;
    }
}

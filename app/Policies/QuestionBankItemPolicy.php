<?php

namespace App\Policies;

use App\Models\QuestionBankItem;
use App\Models\User;

class QuestionBankItemPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isSuperAdmin();
    }

    public function view(User $user, QuestionBankItem $item): bool
    {
        return $user->isSuperAdmin();
    }

    public function create(User $user): bool
    {
        return $user->isSuperAdmin();
    }

    public function update(User $user, QuestionBankItem $item): bool
    {
        return $user->isSuperAdmin();
    }

    public function delete(User $user, QuestionBankItem $item): bool
    {
        return $user->isSuperAdmin();
    }
}

<?php

namespace App\Policies;

use App\Models\User;

class SongPolicy
{
    public function create(User $user): bool
    {
        return $user->isAdmin() || $user->isStaff();
    }

    public function update(User $user): bool
    {
        return $user->isAdmin() || $user->isStaff();
    }
}

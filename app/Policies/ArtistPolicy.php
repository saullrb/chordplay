<?php

namespace App\Policies;

use App\Models\User;

class ArtistPolicy
{
    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    public function store(User $user): bool
    {
        return $user->isAdmin();
    }
}

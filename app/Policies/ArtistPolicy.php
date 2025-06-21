<?php

namespace App\Policies;

use App\Models\User;

class ArtistPolicy
{
    public function store(User $user): bool
    {
        return $user->isAdmin();
    }
}

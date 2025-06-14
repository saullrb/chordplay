<?php

namespace App\Policies;

use App\Models\SongSubmission;
use App\Models\User;

class SongSubmissionPolicy
{
    public function index(User $user): bool
    {
        return $user->isAdmin();
    }

    public function view(User $user, SongSubmission $songSubmission): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        return $user->id === $songSubmission->user_id;
    }

    public function update(User $user, SongSubmission $songSubmission): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        return $user->id === $songSubmission->user_id;
    }

    public function delete(User $user, SongSubmission $songSubmission): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        return $user->id === $songSubmission->user_id;
    }

    public function approve(User $user, SongSubmission $songSubmission): bool
    {
        return $user->isAdmin();
    }
}

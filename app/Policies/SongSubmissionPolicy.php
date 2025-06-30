<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\SongSubmission;
use App\Models\User;

class SongSubmissionPolicy
{
    public function view(User $user, SongSubmission $songSubmission): bool
    {
        return $this->isAdminOrAuthor($user, $songSubmission);
    }

    public function update(User $user, SongSubmission $songSubmission): bool
    {
        return $this->isAdminOrAuthor($user, $songSubmission);
    }

    public function delete(User $user, SongSubmission $songSubmission): bool
    {
        return $this->isAdminOrAuthor($user, $songSubmission);
    }

    public function approve(User $user): bool
    {
        return $this->isAdmin($user);
    }

    private function isAuthor(User $user, SongSubmission $songSubmission): bool
    {
        return $user->id === $songSubmission->user_id;
    }

    private function isAdmin(User $user): bool
    {
        return $user->isAdmin();
    }

    private function isAdminOrAuthor(User $user, SongSubmission $songSubmission): bool
    {
        if ($this->isAdmin($user)) {
            return true;
        }

        return $this->isAuthor($user, $songSubmission);
    }
}

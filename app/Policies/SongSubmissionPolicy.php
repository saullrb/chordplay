<?php

namespace App\Policies;

use App\Models\SongSubmission;
use App\Models\User;

class SongSubmissionPolicy
{
    public function view(User $user, SongSubmission $song_submission): bool
    {
        return $this->isAdminOrAuthor($user, $song_submission);
    }

    public function update(User $user, SongSubmission $song_submission): bool
    {
        return $this->isAdminOrAuthor($user, $song_submission);
    }

    public function delete(User $user, SongSubmission $song_submission): bool
    {
        return $this->isAdminOrAuthor($user, $song_submission);
    }

    public function approve(User $user): bool
    {
        return $this->isAdmin($user);
    }

    private function isAuthor(User $user, SongSubmission $song_submission): bool
    {
        return $user->id === $song_submission->user_id;
    }

    private function isAdmin(User $user): bool
    {
        return $user->isAdmin();
    }

    private function isAdminOrAuthor(User $user, SongSubmission $song_submission): bool
    {
        if ($this->isAdmin($user)) {
            return true;
        }

        return $this->isAuthor($user, $song_submission);
    }
}

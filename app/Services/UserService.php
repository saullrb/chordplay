<?php

namespace App\Services;

use App\Models\Artist;
use App\Models\Song;
use App\Models\User;

class UserService
{
    public function update(User $user, array $data): void
    {
        $user->fill($data)->save();
    }

    public function destroy(User $user): void
    {
        $user->delete();
    }

    public function favoriteArtist(User $user, Artist $artist): void
    {
        $user->addFavoriteArtist($artist);
    }

    public function unfavoriteArtist(User $user, Artist $artist): void
    {
        $user->removeFavoriteArtist($artist);
    }

    public function favoriteSong(User $user, Song $song): void
    {
        $user->addFavoriteSong($song);
    }

    public function unfavoriteSong(User $user, Song $song): void
    {
        $user->removeFavoriteSong($song);
    }
}

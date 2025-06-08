<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'role_id',
    ];

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    public function getFavorites(): array
    {
        return [
            'favorite_songs' => $this->favoriteSongs,
            'favorite_artists' => $this->favoriteArtists,
        ];
    }

    public function favoriteSongs(): BelongsToMany
    {
        return $this->belongsToMany(Song::class, 'favorite_songs')
            ->with('artist:id,name,slug')
            ->select('songs.id', 'songs.name', 'songs.slug', 'songs.artist_id');

    }

    public function favoriteArtists(): BelongsToMany
    {
        return $this->belongsToMany(Artist::class, 'favorite_artists')->select('artists.id', 'artists.name', 'artists.slug');
    }

    public function addFavoriteArtist(Artist $artist)
    {
        $this->favoriteArtists()->attach($artist);
    }

    public function addFavoriteSong(Song $song)
    {
        $this->favoriteSongs()->attach($song);
    }

    public function removeFavoriteArtist(Artist $artist)
    {
        $this->favoriteArtists()->detach($artist);
    }

    public function removeFavoriteSong(Song $song)
    {
        $this->favoriteSongs()->detach($song);
    }

    public function isAdmin(): bool
    {
        return $this->role->id === Role::ADMIN;
    }
}

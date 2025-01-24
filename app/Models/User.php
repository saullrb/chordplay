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
        'password',
        'role_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    public function favoriteSongs(): BelongsToMany
    {
        return $this->belongsToMany(Song::class, 'favorite_songs');
    }

    public function favoriteArtists(): BelongsToMany
    {
        return $this->belongsToMany(Artist::class, 'favorite_artists');
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

    public function isStaff(): bool
    {
        return $this->role->id === Role::STAFF;
    }

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}

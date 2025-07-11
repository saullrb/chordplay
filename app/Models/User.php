<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Notifications\DatabaseNotificationCollection;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

/**
 * @property int $id
 * @property int $role_id
 * @property string $name
 * @property string $email
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection<int, Artist> $favoriteArtists
 * @property-read int|null $favorite_artists_count
 * @property-read Collection<int, Song> $favoriteSongs
 * @property-read int|null $favorite_songs_count
 * @property-read DatabaseNotificationCollection<int,DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read Role|null $role
 */
class User extends Authenticatable
{
    /** @use \Illuminate\Database\Eloquent\Factories\HasFactory<\Database\Factories\UserFactory> */
    use HasFactory;

    use Notifiable;

    protected $fillable = [
        'name',
        'email',
        'avatar_url',
        'role_id',
    ];

    /**
     * @return BelongsTo<Role, $this>
     */
    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * @return array{favorite_songs: Collection<int, Song>, favorite_artists: Collection<int, Artist>}
     */
    public function getFavorites(): array
    {
        return [
            'favorite_songs' => $this->favoriteSongs,
            'favorite_artists' => $this->favoriteArtists,
        ];
    }

    /**
     * @return BelongsToMany<Song, $this>
     */
    public function favoriteSongs(): BelongsToMany
    {
        return $this->belongsToMany(Song::class, 'favorite_songs')
            ->withTimestamps()
            ->with('artist:id,name,slug')
            ->select('songs.id', 'songs.name', 'songs.slug', 'songs.artist_id')
            ->orderBy('favorite_songs.created_at', 'desc');
    }

    /**
     * @return BelongsToMany<Artist, $this>
     */
    public function favoriteArtists(): BelongsToMany
    {
        return $this->belongsToMany(Artist::class, 'favorite_artists')
            ->withTimestamps()
            ->select('artists.id', 'artists.name', 'artists.slug')
            ->orderBy('favorite_artists.created_at', 'desc');
    }

    public function addFavoriteArtist(Artist $artist): void
    {
        $this->favoriteArtists()->attach($artist);
    }

    public function addFavoriteSong(Song $song): void
    {
        $this->favoriteSongs()->attach($song);
    }

    public function removeFavoriteArtist(Artist $artist): void
    {
        $this->favoriteArtists()->detach($artist);
    }

    public function removeFavoriteSong(Song $song): void
    {
        $this->favoriteSongs()->detach($song);
    }

    public function isAdmin(): bool
    {
        return $this->role_id === Role::ADMIN;
    }
}

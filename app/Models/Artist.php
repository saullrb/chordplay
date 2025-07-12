<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Str;

/**
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property string $profile_image_url
 * @property int $views
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection<int, Song> $songs
 * @property-read int|null $songs_count
 */
class Artist extends Model
{
    /** @use HasFactory<\Database\Factories\ArtistFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'profile_image_url',
    ];

    protected $hidden = [
        'pivot',
    ];

    /**
     * @return HasMany<Song, Artist>
     */
    public function songs(): HasMany
    {
        /** @var HasMany<Song, Artist> */
        return $this->hasMany(Song::class);
    }

    #[\Illuminate\Database\Eloquent\Attributes\Scope]
    protected function searchByName(Builder $query, string $search): Builder
    {
        return $query->whereRaw('LOWER(name) LIKE ?', ['%'.strtolower($search).'%']);
    }

    #[\Illuminate\Database\Eloquent\Attributes\Scope]
    protected function withFavoriteStatus(Builder $query, ?int $user_id): Builder
    {
        return $query
            ->leftJoin('favorite_artists', function ($join) use ($user_id): void {
                $join
                    ->on('artists.id', '=', 'favorite_artists.artist_id')
                    ->where('favorite_artists.user_id', '=', $user_id);
            })->select(
                'artists.name',
                'artists.slug',
                'artists.profile_image_url',
                'artists.views',
                \DB::raw('CASE WHEN favorite_artists.artist_id IS NOT NULL THEN 1 ELSE 0 END as is_favorited')
            );
    }

    #[\Illuminate\Database\Eloquent\Attributes\Scope]
    protected function orderByFavoritesAndViews(Builder $query): Builder
    {
        return $query
            ->orderByRaw('CASE WHEN favorite_artists.artist_id IS NOT NULL THEN 0 ELSE 1 END')
            ->orderBy('views', 'desc');
    }

    #[\Override]
    protected static function boot()
    {
        parent::boot();

        static::creating(function (Artist $artist): void {
            $artist->slug = Str::slug($artist->name);
        });

        static::updating(function (Artist $artist): void {
            if ($artist->isDirty('name')) {
                $artist->slug = Str::slug($artist->name);
            }
        });

        static::saving(function (Artist $artist): void {
            $artist->name = Str::squish($artist->name);
        });
    }

    protected function casts(): array
    {
        return [
            'is_favorited' => 'boolean',
        ];
    }
}

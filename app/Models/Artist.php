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
    ];

    protected $hidden = [
        'pivot',
    ];

    protected $casts = [
        'is_favorited' => 'boolean',
    ];

    /**
     * @return HasMany<Song, Artist>
     */
    public function songs(): HasMany
    {
        /** @var HasMany<Song, Artist> */
        return $this->hasMany(Song::class);
    }

    public function scopeSearchByName(Builder $query, string $search): Builder
    {
        return $query->whereRaw('LOWER(name) LIKE ?', ['%'.strtolower($search).'%']);
    }

    public function scopeWithFavoriteStatus(Builder $query, ?int $user_id): Builder
    {
        return $query
            ->leftJoin('favorite_artists', function ($join) use ($user_id): void {
                $join
                    ->on('artists.id', '=', 'favorite_artists.artist_id')
                    ->where('favorite_artists.user_id', '=', $user_id);
            })->select(
                'artists.name',
                'artists.slug',
                \DB::raw('CASE WHEN favorite_artists.artist_id IS NOT NULL THEN 1 ELSE 0 END as is_favorited')
            );
    }

    public function scopeOrderByFavoritesAndViews(Builder $query): Builder
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
            $artist->slug = static::generateSlug($artist->name);
        });

        static::updating(function (Artist $artist): void {
            if ($artist->isDirty('name')) {
                $artist->slug = static::generateSlug($artist->name);
            }
        });

        static::saving(function (Artist $artist): void {
            $artist->name = trim($artist->name);
        });
    }

    protected static function generateSlug(string $name): string
    {
        $baseSlug = Str::slug($name);
        $slug = $baseSlug;

        $count = Artist::whereName($name)->count();

        if ($count > 0) {
            return $baseSlug.'-'.$count;
        }

        return $slug;
    }
}

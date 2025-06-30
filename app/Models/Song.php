<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\SongKeyEnum;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Str;

/**
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property int $artist_id
 * @property SongKeyEnum $key
 * @property int $views
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Artist $artist
 * @property-read Collection<int, SongLine> $lines
 * @property-read int|null $lines_count
 */
class Song extends Model
{
    /** @use HasFactory<\Database\Factories\SongFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'artist_id',
        'key',
    ];

    protected $hidden = [
        'pivot',
    ];

    /**
     * @return BelongsTo<Artist, Song>
     */
    public function artist(): BelongsTo
    {
        /** @var BelongsTo<Artist, Song> */
        return $this->belongsTo(Artist::class);
    }

    /**
     * @return HasMany<SongLine, Song>
     */
    public function lines(): HasMany
    {
        /** @var HasMany<SongLine, Song> */
        return $this->hasMany(SongLine::class)->orderBy('line_number');
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
            ->leftJoin('favorite_songs', function ($join) use ($user_id): void {
                $join
                    ->on('songs.id', '=', 'favorite_songs.song_id')
                    ->where('favorite_songs.user_id', '=', $user_id);
            })->select(
                'songs.name',
                'songs.slug',
                'songs.artist_id',
                'songs.views',
                \DB::raw('CASE WHEN favorite_songs.song_id IS NOT NULL THEN 1 ELSE 0 END as is_favorited')
            );
    }

    #[\Illuminate\Database\Eloquent\Attributes\Scope]
    protected function orderByFavoritesAndViews(Builder $query): Builder
    {
        return $query->orderByRaw('CASE WHEN favorite_songs.song_id IS NOT NULL THEN 0 ELSE 1 END')
            ->orderBy('views', 'desc');
    }

    #[\Override]
    protected static function boot()
    {
        parent::boot();

        static::creating(function (Song $song): void {
            $song->slug = static::generateSlug($song);
        });

        static::updating(function (Song $song): void {
            if ($song->isDirty('name')) {
                $song->slug = static::generateSlug($song);
            }
        });

        static::saving(function (Song $song): void {
            $song->name = trim($song->name);
        });
    }

    protected static function generateSlug(Song $song): string
    {
        $baseSlug = Str::slug($song->name);
        $slug = $baseSlug;

        $count = Song::whereArtistId($song->artist_id)->whereName($song->name)->count();

        if ($count > 0) {
            return $baseSlug.'-'.$count;
        }

        return $slug;
    }

    protected function casts(): array
    {
        return [
            'is_favorited' => 'boolean',
            'key' => SongKeyEnum::class,
        ];
    }
}

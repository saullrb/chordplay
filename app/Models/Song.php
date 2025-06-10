<?php

declare(strict_types=1);

namespace App\Models;

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
 * @property string $key
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
}

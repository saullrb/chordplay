<?php

declare(strict_types=1);

namespace App\Models;

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

    /**
     * @return HasMany<Song, Artist>
     */
    public function songs(): HasMany
    {
        /** @var HasMany<Song, Artist> */
        return $this->hasMany(Song::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Str;

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

    public function artist(): BelongsTo
    {
        return $this->belongsTo(Artist::class);
    }

    public function lines(): HasMany
    {
        return $this->hasMany(SongLine::class)->orderBy('line_number');
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function (Song $song) {
            $song->slug = static::generateSlug($song);
        });

        static::updating(function (Song $song) {
            if ($song->isDirty('name')) {
                $song->slug = static::generateSlug($song);
            }
        });
    }

    private static function generateSlug(Song $song): string
    {
        $baseSlug = Str::slug($song->name);
        $slug = $baseSlug;

        $count = Song::whereArtistId($song->artist_id)->whereName($song->name)->count();

        if ($count > 0) {
            $slug = $baseSlug.'-'.$count;
        }

        return $slug;
    }
}

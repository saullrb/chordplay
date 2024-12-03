<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Str;

class Song extends Model
{
    /** @use HasFactory<\Database\Factories\SongFactory> */
    use HasFactory;

    protected $fillable = [
        "title",
        "slug",
        "artist_id",
        "key"
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function (Song $song) {
            $song->slug = static::generateSlug($song);
        });

        static::updating(function (Song $song) {
            if ($song->isDirty('title')) {
                $song->slug = static::generateSlug($song);
            }
        });
    }

    private static function generateSlug(Song $song): String
    {
        $baseSlug = Str::slug($song->title);
        $slug = $baseSlug;

        $count = Song::whereArtistId($song->artist_id)->whereTitle($song->title)->count();

        if ($count > 0) {
            $slug = $baseSlug . "-" . $count;
        }

        return $slug;
    }
}

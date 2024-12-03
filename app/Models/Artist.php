<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Str;

class Artist extends Model
{
    /** @use HasFactory<\Database\Factories\ArtistFactory> */
    use HasFactory;

    protected $fillable = [
        "name",
        "slug"
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function (Artist $artist) {
            $artist->slug = static::generateSlug($artist->name);
        });

        static::updating(function (Artist $artist) {
            if ($artist->isDirty('name')) {
                $artist->slug = static::generateSlug($artist->name);
            }
        });
    }

    private static function generateSlug(String $name): String
    {
        $baseSlug = Str::slug($name);
        $slug = $baseSlug;

        $count = Artist::whereName($name)->count();

        if ($count > 0) {
            $slug = $baseSlug . "-" . $count;
        }

        return $slug;
    }

    public function songs(): HasMany
    {
        return $this->hasMany(Song::class);
    }
}

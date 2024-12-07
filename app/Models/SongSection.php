<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SongSection extends Model
{
    /** @use HasFactory<\Database\Factories\SongSectionFactory> */
    use HasFactory;

    protected $fillable = [
        "is_lyrical",
        "order",
        "song_id"
    ];

    public function lyricLines()
    {
        return $this->hasMany(LyricLine::class);
    }

    public function chordSequence()
    {
        return $this->hasMany(ChordSequence::class);
    }
}

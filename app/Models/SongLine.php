<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SongLine extends Model
{
    /** @use HasFactory<\Database\Factories\LyricLineFactory> */
    use HasFactory;

    protected $fillable = ['sequence', 'lyrics'];

    public function chords()
    {
        return $this->hasMany(LineChord::class);
    }
}

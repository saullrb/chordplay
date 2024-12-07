<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LyricLine extends Model
{
    /** @use HasFactory<\Database\Factories\LyricLineFactory> */
    use HasFactory;

    public function chordPlacements()
    {
        return $this->hasMany(ChordPlacement::class);
    }
}

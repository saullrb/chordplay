<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LineChord extends Model
{
    /** @use HasFactory<\Database\Factories\ChordPlacementFactory> */
    use HasFactory;

    public function chord()
    {
        return $this->belongsTo(Chord::class);
    }
}

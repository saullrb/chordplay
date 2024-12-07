<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChordSequence extends Model
{
    /** @use HasFactory<\Database\Factories\ChordSequenceFactory> */
    use HasFactory;

    public function chord()
    {
        return $this->belongsTo(Chord::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Chord extends Model
{
    public function simplifiedChord(): HasOne
    {
        return $this->hasOne(Chord::class, 'simplified_chord_id');
    }

    public function shapes(): HasMany
    {
        return $this->hasMany(ChordShape::class);
    }
}

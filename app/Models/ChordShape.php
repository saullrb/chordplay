<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChordShape extends Model
{
    public function chord(): BelongsTo
    {
        return $this->belongsTo(Chord::class);
    }
}

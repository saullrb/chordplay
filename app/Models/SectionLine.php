<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SectionLine extends Model
{
    /** @use HasFactory<\Database\Factories\LyricLineFactory> */
    use HasFactory;

    public function lineChords()
    {
        return $this->hasMany(LineChord::class);
    }
}

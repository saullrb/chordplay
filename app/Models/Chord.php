<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Chord extends Model
{
    protected $fillable = ['name', 'variation'];
    public const VARIATIONS = [
        'major' => '',
        'minor' => 'm',
        'seventh' => '7',
        'minor_seventh' => 'm7',
        'major_seventh' => 'maj7',
        'ninth' => '9',
        'diminished' => '°',
        'augmented' => '+',
    ];

    public const BASE_NOTES = ['A', 'A#', 'B', 'C', 'C#', 'D', 'D#', 'E', 'F', 'F#', 'G', 'G#'];

    public static function getGroupedChords()
    {
        return self::get()->groupBy('variation')->map(function ($chords) {
            return $chords->pluck('name');
        });
    }
}

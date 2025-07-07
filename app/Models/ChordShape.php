<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChordShape extends Model
{
    protected $fillable = [
        'chord_id',
        'frets',
        'fingers',
        'barres',
        'midi',
        'base_fret',
        'capo',
    ];

    protected function casts(): array
    {
        return [
            'frets' => 'array',
            'fingers' => 'array',
            'barres' => 'array',
            'midi' => 'array',
            'capo' => 'boolean',
        ];
    }
}

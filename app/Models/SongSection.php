<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SongSection extends Model
{
    /** @use HasFactory<\Database\Factories\SongSectionFactory> */
    use HasFactory;

    protected $fillable = [
        'sequence',
        'song_id',
    ];

    public function sectionLines()
    {
        return $this->hasMany(SectionLine::class);
    }
}

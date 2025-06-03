<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SongLine extends Model
{
    protected $fillable = [
        'line_number',
        'song_id',
        'content_type',
        'content',
    ];

    public function song(): BelongsTo
    {
        return $this->belongsTo(Song::class);
    }
}

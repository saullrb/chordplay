<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $song_id
 * @property int $line_number
 * @property string $content_type
 * @property string $content
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Song $song
 */
class SongLine extends Model
{
    /** @use HasFactory<\Database\Factories\SongLineFactory> */
    use HasFactory;

    protected $fillable = [
        'line_number',
        'song_id',
        'content_type',
        'content',
    ];

    /**
     * @return BelongsTo<Song, self>
     */
    public function song(): BelongsTo
    {
        /** @var BelongsTo<Song, SongLine> */
        return $this->belongsTo(Song::class);
    }
}

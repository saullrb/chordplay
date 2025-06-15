<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $song_submission_id
 * @property int $line_number
 * @property string $content_type
 * @property string $content
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read SongSubmission $song
 */
class SongSubmissionLine extends Model
{
    protected $fillable = [
        'line_number',
        'song_id',
        'content_type',
        'content',
    ];

    /**
     * @return BelongsTo<SongSubmission, self>
     */
    public function song(): BelongsTo
    {
        /** @var BelongsTo<SongSubmission, SongSubmissionLine> */
        return $this->belongsTo(SongSubmission::class);
    }
}

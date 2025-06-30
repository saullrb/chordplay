<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\SongKeyEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

/**
 * @property int $id
 * @property string $name
 * @property SongKeyEnum $key
 * @property int $artist_id
 * @property int $user_id
 * @property Carbon|null $created_at
 * @property-read Artist $artist
 * @property-read Collection<int, SongSubmissionLine> $lines
 * @property-read int|null $lines_count
 */
class SongSubmission extends Model
{
    /** @use HasFactory<\Database\Factories\SongSubmissionFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'artist_id',
        'user_id',
        'song_id',
        'key',
    ];

    /**
     * @return BelongsTo<Artist, SongSubmission>
     */
    public function artist(): BelongsTo
    {
        /** @var BelongsTo<Artist, SongSubmission> */
        return $this->belongsTo(Artist::class);
    }

    /**
     * @return BelongsTo<User, SongSubmission>
     */
    public function user(): BelongsTo
    {
        /** @var BelongsTo<User, SongSubmission> */
        return $this->belongsTo(User::class);
    }

    /**
     * @return HasMany<SongSubmissionLine, SongSubmission>
     */
    public function lines(): HasMany
    {
        /** @var HasMany<SongSubmissionLine, SongSubmission> */
        return $this->hasMany(SongSubmissionLine::class)->orderBy('line_number');
    }

    protected function casts(): array
    {
        return [
            'key' => SongKeyEnum::class,
        ];
    }
}

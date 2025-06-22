<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

/**
 * @property int $id
 * @property string $name
 * @property string $variation
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class Chord extends Model
{
    protected $fillable = ['name', 'variation'];

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'name' => 'string',
        'variation' => 'string',
    ];

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

    public static function allChords(): array
    {
        $chords = [];

        foreach (Chord::BASE_NOTES as $base_note) {
            foreach (Chord::VARIATIONS as $variation) {
                $chord = $base_note.$variation;
                $chords[] = $chord;
            }
        }

        return $chords;
    }

    /**
     * @return Collection<int|string, Collection<int|string, mixed>>
     */
    public static function getGroupedChords(): Collection
    {
        return self::get()
            ->groupBy('variation')
            ->map(fn ($chords) => $chords->pluck('name'));
    }
}

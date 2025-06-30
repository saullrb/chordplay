<?php

namespace App\Enums;

enum SongLineContentType: string
{
    case LYRICS = 'lyrics';
    case CHORDS = 'chords';
    case EMPTY = 'empty';

    /**
     * @return array<string>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}

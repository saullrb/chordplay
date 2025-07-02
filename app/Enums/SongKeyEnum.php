<?php

declare(strict_types=1);

namespace App\Enums;

enum SongKeyEnum: string
{
    case A = 'A';
    case A_MINOR = 'Am';
    case A_SHARP = 'A#';
    case A_SHARP_MINOR = 'A#m';
    case B_FLAT = 'Bb';
    case B_FLAT_MINOR = 'Bbm';
    case B = 'B';
    case B_MINOR = 'Bm';
    case C = 'C';
    case C_MINOR = 'Cm';
    case C_SHARP = 'C#';
    case C_SHARP_MINOR = 'C#m';
    case D_FLAT = 'Db';
    case D_FLAT_MINOR = 'Dbm';
    case D = 'D';
    case D_MINOR = 'Dm';
    case D_SHARP = 'D#';
    case D_SHARP_MINOR = 'D#m';
    case E_FLAT = 'Eb';
    case E_FLAT_MINOR = 'Ebm';
    case E = 'E';
    case E_MINOR = 'Em';
    case F = 'F';
    case F_MINOR = 'Fm';
    case F_SHARP = 'F#';
    case F_SHARP_MINOR = 'F#m';
    case G_FLAT = 'Gb';
    case G_FLAT_MINOR = 'Gbm';
    case G = 'G';
    case G_MINOR = 'Gm';
    case G_SHARP = 'G#';
    case G_SHARP_MINOR = 'G#m';
    case A_FLAT = 'Ab';
    case A_FLAT_MINOR = 'Abm';

    /**
     *  Get all the values
     *
     * @return array<string>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    /**
     * Check if this key is in minor mode
     */
    public function isMinor(): bool
    {
        return str_ends_with($this->value, 'm');
    }

    /**
     * Check if this key is in major mode
     */
    public function isMajor(): bool
    {
        return ! $this->isMinor();
    }

    /**
     * Get the root note without mode indicator
     */
    public function getRoot(): string
    {
        return str_replace('m', '', $this->value);
    }

    /**
     * Check if this key uses sharp notation
     */
    public function usesSharpNotation(): bool
    {
        return str_contains($this->value, '#');
    }

    /**
     * Check if this key uses flat notation
     */
    public function usesFlatNotation(): bool
    {
        return str_contains($this->value, 'b');
    }

    /**
     * Get all keys that use the same notation system and mode
     *
     * @return array<string>
     */
    public static function getKeysInSameNotation(self $key): array
    {
        $isMinor = $key->isMinor();
        $usesFlats = $key->usesFlatNotation();

        $baseKeys = $usesFlats ? self::getFlatKeys() : self::getSharpKeys();

        return $isMinor
            ? array_map(fn ($k): string => $k.'m', $baseKeys)
            : $baseKeys;
    }

    /**
     * Get keys in sharp notation (chromatic scale)
     *
     * @return array<string>
     */
    private static function getSharpKeys(): array
    {
        return ['A', 'A#', 'B', 'C', 'C#', 'D', 'D#', 'E', 'F', 'F#', 'G', 'G#'];
    }

    /**
     * Get keys in flat notation (chromatic scale)
     *
     * @return array<string>
     */
    private static function getFlatKeys(): array
    {
        return ['A', 'Bb', 'B', 'C', 'Db', 'D', 'Eb', 'E', 'F', 'Gb', 'G', 'Ab'];
    }
}

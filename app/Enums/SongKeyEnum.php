<?php

declare(strict_types=1);

namespace App\Enums;

enum SongKeyEnum: string
{
    case C = 'C';
    case C_MINOR = 'Cm';
    case C_SHARP = 'C#';
    case C_SHARP_MINOR = 'C#m';
    case D = 'D';
    case D_MINOR = 'Dm';
    case E_FLAT = 'Eb';
    case E_FLAT_MINOR = 'Ebm';
    case E = 'E';
    case E_MINOR = 'Em';
    case F = 'F';
    case F_MINOR = 'Fm';
    case F_SHARP = 'F#';
    case F_SHARP_MINOR = 'F#m';
    case G = 'G';
    case G_MINOR = 'Gm';
    case A_FLAT = 'Ab';
    case A_FLAT_MINOR = 'Abm';
    case A = 'A';
    case A_MINOR = 'Am';
    case B_FLAT = 'Bb';
    case B_FLAT_MINOR = 'Bbm';
    case B = 'B';
    case B_MINOR = 'Bm';

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
     * Get all keys that use the same mode
     *
     * @return array<string>
     */
    public static function getKeysInSameMode(self $key): array
    {
        $condition = $key->isMinor()
        ? fn ($case) => $case->isMinor()
        : fn ($case) => $case->isMajor();

        $keys = [];
        foreach (self::cases() as $case) {
            if ($condition($case)) {
                $keys[] = $case->value;
            }
        }

        return $keys;
    }
}

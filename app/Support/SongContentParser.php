<?php

declare(strict_types=1);

namespace App\Support;

use App\Enums\SongLineContentType;

class SongContentParser
{
    private const CHORD_START = '[';

    private const CHORD_END = ']';

    public function parse(string $content): array
    {
        $lines = explode(PHP_EOL, trim($content));
        $processed = [];
        $allChords = [];

        foreach ($lines as $i => $line) {
            $trimmed = trim($line);

            if (str_starts_with($trimmed, self::CHORD_START)) {
                $result = $this->processChordLine($line);
                $processed[] = [
                    'line_number' => $i,
                    'content_type' => SongLineContentType::CHORDS,
                    'content' => $result['sanitized'],
                ];
                $allChords += array_fill_keys($result['chords'], true);
            } else {
                $processed[] = [
                    'line_number' => $i,
                    'content_type' => $trimmed === '' || $trimmed === '0'
                        ? SongLineContentType::EMPTY
                        : SongLineContentType::LYRICS,
                    'content' => $trimmed,
                ];
            }
        }

        return [$processed, array_keys($allChords)];
    }

    public function processChordLine(string $line): array
    {
        $inside = false;
        $token = '';
        $sanitized = '';
        $chords = [];

        foreach (str_split($line) as $char) {
            if ($char === self::CHORD_START) {
                if ($inside) {
                    $sanitized = '';
                }
                $inside = true;
                $token = '';
                $sanitized .= $char;
            } elseif ($char === self::CHORD_END && $inside) {
                $inside = false;
                $chords[] = $token;
                $sanitized .= $char;
            } elseif ($inside) {
                if ($char !== ' ') {
                    $token .= $char;
                    $sanitized .= $char;
                }
            } elseif ($char === ' ') {
                $sanitized .= $char;
            }
        }

        return [
            'sanitized' => $sanitized,
            'chords' => $chords,
        ];
    }
}

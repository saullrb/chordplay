<?php

namespace App\Support;

use App\Enums\SongLineContentType;

// TODO: add tests
class SongContentParser
{
    public function parse(string $content): array
    {
        $lines = explode(PHP_EOL, trim($content));
        $processed = [];
        $chord_lines = [];

        foreach ($lines as $i => $line) {
            $trimmed = trim($line);

            if (str_starts_with($trimmed, '[')) {
                $processed[] = [
                    'line_number' => $i,
                    'content_type' => SongLineContentType::CHORDS,
                    'content' => $this->sanitizeChordsLine($line),
                ];
                $chord_lines[] = $line;
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

        return [$processed, $this->extractChords($chord_lines)];
    }

    private function sanitizeChordsLine(string $line): string
    {
        $inside = false;
        $out = '';

        foreach (str_split($line) as $char) {
            if ($char === '[') {
                $inside = true;
                $out .= $char;
            } elseif ($char === ']') {
                $inside = false;
                $out .= $char;
            } elseif ($inside || $char === ' ') {
                $out .= $char;
            }
        }

        return $out;
    }

    private function extractChords(array $lines): array
    {
        $chords = [];

        foreach ($lines as $line) {
            $inside = false;
            $token = '';

            for ($i = 0; $i < strlen((string) $line); $i++) {
                $char = $line[$i];

                if ($char === '[') {
                    $inside = true;
                    $token = '';
                } elseif ($char === ']') {
                    $inside = false;
                    $chords[$token] = true;
                } elseif ($inside) {
                    $token .= $char;
                }
            }
        }

        return array_keys($chords);
    }
}

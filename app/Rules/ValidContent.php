<?php

namespace App\Rules;

use App\Enums\SongLineContentType;
use App\Models\Chord;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ValidContent implements ValidationRule
{
    public array $processed_content = [];

    private array $invalid_chords = [];

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $lines = explode(PHP_EOL, (string) $value);
        $chord_lines = [];
        $processed_lines = [];
        $line_number = 0;

        foreach ($lines as $line) {
            $trimmed_line = trim($line);

            if (str_starts_with($trimmed_line, '[')) {
                $output = '';
                $inside_brackets = false;

                foreach (str_split($line) as $char) {
                    if ($char === '[') {
                        $inside_brackets = true;
                        $output .= $char;
                    } elseif ($char === ']') {
                        $inside_brackets = false;
                        $output .= $char;
                    } elseif ($inside_brackets || $char === ' ') {
                        $output .= $char;
                    }
                }

                $processed_lines[] = [
                    'line_number' => $line_number,
                    'content_type' => SongLineContentType::CHORDS,
                    'content' => $output,
                ];
                $chord_lines[] = $output;
            } else {
                $processed_lines[] = [
                    'line_number' => $line_number,
                    'content' => $trimmed_line,
                    'content_type' => $trimmed_line === '' || $trimmed_line === '0' ? SongLineContentType::EMPTY : SongLineContentType::LYRICS,
                ];
            }

            $line_number++;
        }

        $this->processed_content = $processed_lines;

        $chords = $this->extractChords($chord_lines);

        $valid_chords = Chord::whereIn('name', $chords)->pluck('name')->toArray();

        $this->invalid_chords = array_diff($chords, $valid_chords);

        if ($this->invalid_chords !== []) {
            $fail($this->message());
        }
    }

    private function extractChords(array $chord_lines): array
    {
        $chords = [];

        foreach ($chord_lines as $line) {
            $token = '';
            $inside_brackets = false;

            for ($i = 0; $i < strlen((string) $line); $i++) {
                $char = $line[$i];

                if ($char === '[') {
                    $inside_brackets = true;
                    $token = '';
                } elseif ($char === ']') {
                    $inside_brackets = false;
                    $chords[$token] = true;
                } elseif ($inside_brackets) {
                    $token .= $char;
                }
            }
        }

        return array_keys($chords);
    }

    private function message(): string
    {
        return 'These chords are invalid: '.implode(', ', $this->invalid_chords);
    }
}

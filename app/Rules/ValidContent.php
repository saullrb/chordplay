<?php

namespace App\Rules;

use App\Models\Chord;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ValidContent implements ValidationRule
{
    protected array $invalid_chords = [];

    public ?string $processed_content = null;

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $lines = explode(PHP_EOL, $value);
        $chord_lines = [];
        $processed_lines = [];

        foreach ($lines as $line) {
            $trimmed_line = trim($line);

            if (str_starts_with($trimmed_line, '[')) {
                $output = '';
                $inside_brackets = true;
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

                $processed_lines[] = $output;
                $chord_lines[] = $output;
            } else {
                $processed_lines[] = $trimmed_line;
            }
        }

        $this->processed_content = implode(PHP_EOL, $processed_lines);

        $chords = [];

        foreach ($chord_lines as $line) {
            $token = '';
            $inside_brackets = false;

            for ($i = 0; $i < strlen($line); $i++) {
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

        $chords = array_keys($chords);

        $existing_chords = Chord::whereIn('name', $chords)->pluck('name')->toArray();

        $this->invalid_chords = array_diff($chords, $existing_chords);

        if (! empty($this->invalid_chords)) {
            $fail($this->message());
        }
    }

    public function message()
    {
        return 'These chords are invalid: '.implode(', ', $this->invalid_chords);
    }
}

<?php

namespace App\Rules;

use App\Models\Chord;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ValidChords implements ValidationRule
{
    private array $invalid_chords = [];

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! Chord::whereName($value)->exists()) {
            $fail('Invalid chord '.$value);
        }

        // if (! empty($this->invalid_chords)) {
        //     $fail('Invalid chords found: '.implode(', ', array_unique($this->invalid_chords)));
        // }
    }
}

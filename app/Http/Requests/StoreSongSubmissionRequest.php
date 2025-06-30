<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Enums\SongKeyEnum;
use App\Models\Chord;
use App\Support\SongContentParser;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class StoreSongSubmissionRequest extends FormRequest
{
    private array $processed_lines = [];

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:100'],
            'key' => ['required', new Enum(SongKeyEnum::class)],
            'content' => ['required', 'string', function ($attribute, $value, $fail): void {
                $parser = new SongContentParser;
                [$processed, $chords] = $parser->parse($value);
                $this->processed_lines = $processed;

                $valid = Chord::whereIn('name', $chords)->pluck('name')->toArray();
                $invalid = array_diff($chords, $valid);

                if ($invalid !== []) {
                    $fail('These chords are invalid: '.implode(', ', $invalid));
                }
            }],
        ];
    }

    public function validated($key = null, $default = null): array
    {
        $validated = parent::validated($key, $default);
        unset($validated['content']);
        $validated['lines'] = $this->processed_lines;

        return $validated;
    }
}

<?php

namespace Database\Factories;

use App\Enums\SongLineContentType;
use App\Models\Chord;
use App\Models\Song;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SongLine>
 */
class SongLineFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $contentType = fake()->randomElement(array_column(SongLineContentType::cases(), 'value'));

        return [
            'song_id' => Song::factory(),
            'line_number' => 1,
            'content_type' => $contentType,
            'content' => $this->generateContentForType($contentType),
        ];
    }

    private function generateContentForType(string $contentType): string
    {
        return match ($contentType) {
            'chords' => $this->generateChords(),
            'lyrics' => fake()->sentence(),
            'empty' => '',
            default => '',
        };
    }

    private function generateChords(): string
    {
        $chords = Chord::inRandomOrder()->limit(fake()->numberBetween(3, 5))->pluck('name')->toArray();

        if (empty($chords)) {
            $chords = ['C', 'D', 'E', 'F', 'G', 'A', 'B'];
            $chords = fake()->randomElements($chords, fake()->numberBetween(3, 5));
        }

        $chordLine = '';
        foreach ($chords as $chord) {
            $spacing = str_repeat(' ', fake()->numberBetween(4, 8));
            $chordLine .= "[{$chord}]{$spacing}";
        }

        return trim($chordLine);
    }

    public function chords(): static
    {
        return $this->state(fn (array $attributes): array => [
            'content_type' => 'chords',
            'content' => $this->generateChords(),
        ]);
    }

    public function lyrics(): static
    {
        return $this->state(fn (array $attributes): array => [
            'content_type' => 'lyrics',
            'content' => fake()->sentence(),
        ]);
    }

    public function empty(): static
    {
        return $this->state(fn (array $attributes): array => [
            'content_type' => 'empty',
            'content' => '',
        ]);
    }
}

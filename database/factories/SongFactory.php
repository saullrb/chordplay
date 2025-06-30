<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\SongKeyEnum;
use App\Models\Artist;
use App\Models\Chord;
use App\Models\SongLine;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Song>
 */
class SongFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->words(random_int(3, 5), true),
            'artist_id' => Artist::factory(),
            'key' => fake()->randomElement(SongKeyEnum::cases()),
            'views' => fake()->numberBetween(0, 10000),
        ];
    }

    public function withSongBlocks(int $block_count, ?string $chord_content = null): static
    {
        $lines = [];
        $line_number = 0;

        for ($block = 0; $block < $block_count; $block++) {
            for ($pair = 0; $pair < 4; $pair++) {
                // Chords line
                $lines[] = [
                    'line_number' => $line_number++,
                    'content_type' => 'chords',
                    'content' => $chord_content ?? $this->generateChords(),
                ];

                // Lyrics line
                $lines[] = [
                    'line_number' => $line_number++,
                    'content_type' => 'lyrics',
                    'content' => fake()->sentence(),
                ];
            }

            $lines[] = [
                'line_number' => $line_number++,
                'content_type' => 'empty',
                'content' => '',
            ];
        }

        return $this->has(
            SongLine::factory()
                ->count(count($lines))
                ->sequence(...$lines),
            'lines'
        );
    }

    private function generateChords(): string
    {
        $chords = fake()->randomElements(Chord::allChords(), fake()->numberBetween(3, 5));

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
}

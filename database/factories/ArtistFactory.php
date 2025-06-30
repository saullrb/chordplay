<?php

namespace Database\Factories;

use App\Models\Song;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Artist>
 */
class ArtistFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->company(),
            'views' => fake()->numberBetween(0, 1000),
        ];
    }

    public function withSongs(int $song_count = 1): static
    {
        return $this->has(
            Song::factory()->count($song_count)->withSongBlocks(4)
        );
    }
}

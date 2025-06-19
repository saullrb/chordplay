<?php

namespace Database\Factories;

use App\Enums\SongKeyEnum;
use App\Models\Artist;
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
            'name' => fake()->word().' '.fake()->word,
            'artist_id' => Artist::factory(),
            'key' => fake()->randomElement(array_column(SongKeyEnum::cases(), 'value')),
            'views' => fake()->numberBetween(0, 100),
        ];
    }
}

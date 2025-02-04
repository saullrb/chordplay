<?php

namespace Database\Factories;

use App\Enums\SongKeyEnum;
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
            'key' => fake()->randomElement(array_column(SongKeyEnum::cases(), 'value')),
            'views' => fake()->randomNumber(2),
            'content' => "Am                 G                F
" . fake()->sentence(4) . "
C                  Em               Am
" . fake()->sentence(3) . "
F                  G                Am
" . fake()->sentence(4) . "
Am                 F                G
" . fake()->sentence(3),
        ];
    }
}

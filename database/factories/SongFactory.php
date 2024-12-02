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
            "title" => fake()->colorName(),
            "key" => fake()->randomElement(array_column(SongKeyEnum::cases(), 'name')),
        ];
    }
}

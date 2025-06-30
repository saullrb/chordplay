<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\SongKeyEnum;
use App\Models\Artist;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SongSubmission>
 */
class SongSubmissionFactory extends Factory
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
            'key' => fake()->randomElement(SongKeyEnum::cases()),
            'artist_id' => Artist::factory(),
            'user_id' => User::factory(),
        ];
    }
}

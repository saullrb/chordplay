<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Artist;
use App\Models\SongSubmission;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(ChordSeeder::class);

        $user = User::factory()->create();
        Artist::factory()
            ->count(10)
            ->withSongs(
                fake()->numberBetween(3, 10)
            )
            ->create();

        SongSubmission::factory()->count(5)->create([
            'user_id' => $user->id,
        ]);
    }
}

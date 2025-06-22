<?php

namespace Database\Seeders;

use App\Models\Artist;
use App\Models\Song;
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
        $user = User::factory()->create();
        $artist = Artist::factory()->create();

        Song::factory()
            ->count(20)
            ->withSongBlocks(4)
            ->create([
                'artist_id' => $artist->id,
            ]);

        SongSubmission::factory()->count(5)->create([
            'artist_id' => $artist->id,
            'user_id' => $user->id,
        ]);
    }
}

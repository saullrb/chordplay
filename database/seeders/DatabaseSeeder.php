<?php

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
        $user = User::factory()->create();
        $artist = Artist::factory()->create();
        SongSubmission::factory()->count(10)->create([
            'artist_id' => $artist->id,
            'user_id' => $user->id,
        ]);
    }
}

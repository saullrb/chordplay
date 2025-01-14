<?php

namespace Database\Seeders;

use App\Models\Artist;
use App\Models\Chord;
use App\Models\LineChord;
use App\Models\Song;
use App\Models\SongLine;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $chords = Chord::all();

        Artist::factory()
            ->count(10)
            ->has(
                Song::factory()
                    ->count(5)
                    ->has(
                        SongLine::factory()
                            ->count(20) // Each song has 5 sections
                            ->sequence(fn ($seq) => ['sequence' => $seq->index + 1])
                            ->afterCreating(function (SongLine $line) use ($chords) {
                                // Add 3 random chords per line
                                collect(range(0, strlen($line->lyrics) - 1))
                                    ->random(3)
                                    ->sort()
                                    ->each(fn ($position) => LineChord::factory()->create([
                                        'song_line_id' => $line->id,
                                        'chord_id' => $chords->random()->id,
                                        'position' => $position,

                                    ]));
                            }), 'lines'
                    ),
            )
            ->create();
    }
}

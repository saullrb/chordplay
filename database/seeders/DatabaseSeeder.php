<?php

namespace Database\Seeders;

use App\Models\Artist;
use App\Models\Chord;
use App\Models\LineChord;
use App\Models\SectionLine;
use App\Models\Song;
use App\Models\SongSection;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        collect(['A', 'Am', 'C', 'D', 'Dm', 'E', 'Em', 'F', 'G'])->each(
            fn ($name) => Chord::create(['name' => $name])
        );

        $chords = Chord::all();

        Artist::factory()
            ->count(25)
            ->has(
                Song::factory()
                    ->count(12)
                    ->has(
                        SongSection::factory()
                            ->count(5) // Each song has 5 sections
                            ->afterCreating(function (SongSection $section) use ($chords) {
                                SectionLine::factory()
                                    ->count(4)
                                    ->sequence(fn ($seq) => ['sequence' => $seq->index + 1])
                                    ->create(['song_section_id' => $section->id])
                                    ->each(function ($line) use ($chords) {
                                        // Add 2 random chords per line
                                        collect(range(0, strlen($line->lyrics) - 1))
                                            ->random(3)
                                            ->sort()
                                            ->each(fn ($position) => LineChord::factory()->create([
                                                'section_line_id' => $line->id,
                                                'chord_id' => $chords->random()->id,
                                                'position' => $position,

                                            ]));
                                    });
                            }),
                        'sections'
                    )
            )
            ->create();
    }
}

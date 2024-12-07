<?php

namespace Database\Seeders;

use App\Models\Artist;
use App\Models\Chord;
use App\Models\ChordPlacement;
use App\Models\ChordSequence;
use App\Models\LyricLine;
use App\Models\Song;
use App\Models\SongSection;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        collect(['A', 'Am', 'C', 'D', 'Dm', 'E', 'Em', 'F', 'G'])->each(
            fn($name) => Chord::create(['name' => $name])
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
                            ->state(function () {
                                return [
                                    'is_lyrical' => fake()->boolean(80), // 80% chance of being lyrical
                                ];
                            })
                            ->afterCreating(function (SongSection $section) use ($chords) {
                                if ($section->is_lyrical) {
                                    // Create 4 lines with chords
                                    LyricLine::factory()
                                        ->count(4)
                                        ->sequence(fn($seq) => ['order' => $seq->index + 1])
                                        ->create(['song_section_id' => $section->id])
                                        ->each(function ($line) use ($chords) {
                                            // Add 2 random chords per line
                                            collect(range(0, strlen($line->text) - 1))
                                                ->random(3)
                                                ->sort()
                                                ->each(fn($position) => ChordPlacement::factory()->create([
                                                    'lyric_line_id' => $line->id,
                                                    'chord_id' => $chords->random()->id,
                                                    'position' => $position

                                                ]));
                                        });
                                } else {
                                    // Create 4 chords in sequence
                                    ChordSequence::factory()
                                        ->count(4)
                                        ->sequence(fn($seq) => ['order' => $seq->index + 1])
                                        ->create([
                                            'song_section_id' => $section->id,
                                            'chord_id' => $chords->random()->id,
                                        ]);
                                }
                            }),
                        'sections'
                    )
            )
            ->create();

        // Artist::factory(30)->has(Song::factory(20)->has(SongSection::factory(5), 'sections'))->create();
    }
}

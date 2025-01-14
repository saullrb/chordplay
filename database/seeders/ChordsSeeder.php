<?php

namespace Database\Seeders;

use App\Models\Chord;
use Illuminate\Database\Seeder;

class ChordsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $base_chords = ['A', 'A#',  'B', 'C', 'C#', 'D', 'D#', 'E', 'F', 'F#', 'G', 'G#'];
        $variations = ['m', '7', 'm7', 'maj7', '9', '°', '+'];

        foreach ($base_chords as $base_chord) {
            foreach ($variations as $variation) {
                $chord = $base_chord.$variation;
                Chord::create(['name' => $chord]);
            }
        }
    }
}

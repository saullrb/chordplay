<?php

namespace Database\Seeders;

use App\Models\Chord;
use Illuminate\Database\Seeder;

class ChordSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach (Chord::BASE_NOTES as $base_note) {
            foreach (Chord::VARIATIONS as $variation) {
                $chord = $base_note.$variation;
                Chord::create(['name' => $chord, 'variation' => $variation]);
            }
        }
    }
}

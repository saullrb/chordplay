<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class ChordSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $json = File::get(resource_path('data/guitar.json'));
        $data = json_decode($json, true);
        throw_if(json_last_error() !== JSON_ERROR_NONE, new \Exception('Failed to parse guitar.json: '.json_last_error_msg()));

        $chords = [];
        $chordShapes = [];
        $chordUpdates = [];
        $chordIdCounter = 1;
        $shapeIdCounter = 1;

        foreach ($data['chords'] as $chordGroup) {
            foreach ($chordGroup as $chord) {
                $suffix = $chord['suffix'];
                if ($chord['suffix'] === 'major') {
                    $suffix = '';
                } elseif ($chord['suffix'] === 'minor') {
                    $suffix = 'm';
                }

                $chordId = $chordIdCounter++;
                $chords[] = [
                    'id' => $chordId,
                    'name' => $chord['key'].$suffix,
                    'key' => $chord['key'],
                    'suffix' => $suffix,
                    'default_shape_id' => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];

                $defaultShapeId = null;

                foreach ($chord['positions'] as $index => $position) {
                    $shapeId = $shapeIdCounter++;

                    // First position becomes default
                    if ($index === 0) {
                        $defaultShapeId = $shapeId;
                    }

                    $chordShapes[] = [
                        'id' => $shapeId,
                        'chord_id' => $chordId,
                        'frets' => json_encode($position['frets']),
                        'fingers' => json_encode($position['fingers']),
                        'barres' => json_encode($position['barres']),
                        'midi' => json_encode($position['midi']),
                        'base_fret' => $position['baseFret'],
                        'capo' => $position['capo'] ?? false,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }

                // Store update for default_shape_id
                $chordUpdates[] = [
                    'id' => $chordId,
                    'default_shape_id' => $defaultShapeId,
                ];
            }
        }

        DB::transaction(function () use ($chords, $chordShapes, $chordUpdates): void {
            collect($chords)->chunk(1000)->each(function ($chunk): void {
                DB::table('chords')->insert($chunk->toArray());
            });

            collect($chordShapes)->chunk(1000)->each(function ($chunk): void {
                DB::table('chord_shapes')->insert($chunk->toArray());
            });

            if (! empty($chordUpdates)) {
                $sql = 'UPDATE chords SET default_shape_id = CASE id ';
                $ids = [];
                foreach ($chordUpdates as $update) {
                    $sql .= "WHEN {$update['id']} THEN {$update['default_shape_id']} ";
                    $ids[] = $update['id'];
                }
                $sql .= 'END WHERE id IN ('.implode(',', $ids).')';
                DB::statement($sql);
            }
        });
    }
}

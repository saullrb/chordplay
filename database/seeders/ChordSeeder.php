<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Chord;
use Illuminate\Database\Seeder;
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

        $records = [];
        foreach ($data['chords'] as $chords) {
            foreach ($chords as $chord) {
                $suffix = $chord['suffix'];

                if ($chord['suffix'] === 'major') {
                    $suffix = '';
                } elseif ($chord['suffix'] === 'minor') {
                    $suffix = 'm';
                }

                $records[] = [
                    'name' => $chord['key'].$suffix,
                    'key' => $chord['key'],
                    'suffix' => $suffix,
                    'positions' => json_encode($chord['positions']),
                ];
            }
        }

        collect($records)->chunk(200)->each(function ($chunk): void {
            Chord::insert($chunk->toArray());
        });
    }
}

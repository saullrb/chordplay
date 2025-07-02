<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Song;
use App\Models\SongSubmission;
use Illuminate\Support\Facades\DB;

class SongSubmissionService
{
    public function store(array $songSubmission, int $artist_id, int $user_id): SongSubmission
    {
        $submission = SongSubmission::create([
            'song_id' => $songSubmission['songId'] ?? null,
            'artist_id' => $artist_id,
            'user_id' => $user_id,
            'name' => $songSubmission['name'],
            'key' => $songSubmission['key'],
        ]);

        foreach ($songSubmission['lines'] as $line) {
            $submission->lines()->create($line);
        }

        return $submission;
    }

    public function update(SongSubmission $songSubmission, array $validated): SongSubmission
    {
        return DB::transaction(function () use ($songSubmission, $validated): SongSubmission {
            $songSubmission->updateOrFail($validated);
            $songSubmission->lines()->delete();
            $songSubmission->lines()->createMany($validated['lines']);

            return $songSubmission;
        });
    }

    public function approve(SongSubmission $songSubmission): Song
    {
        $songSubmission->load([
            'lines' => fn ($query) => $query->orderBy('line_number'),
        ]);

        return DB::transaction(function () use ($songSubmission) {
            // Check if is creating a new song or updating a existing one
            if ($songSubmission->song_id) {
                $song = Song::find($songSubmission->song_id);
                $song->update([
                    'name' => $songSubmission->name,
                    'key' => $songSubmission->key,
                ]);

                $song->lines()->delete();
            } else {
                $song = Song::create([
                    'name' => $songSubmission->name,
                    'key' => $songSubmission->key,
                    'artist_id' => $songSubmission->artist_id,
                ]);
            }

            foreach ($songSubmission->lines as $line) {
                $song->lines()->create([
                    'line_number' => $line->line_number,
                    'content' => $line->content,
                    'content_type' => $line->content_type,
                ]);
            }

            $songSubmission->delete();

            return $song;
        });
    }

    public function destroy(SongSubmission $songSubmission): void
    {
        $songSubmission->delete();
    }
}

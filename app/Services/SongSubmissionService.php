<?php

namespace App\Services;

use App\Models\Song;
use App\Models\SongSubmission;
use Illuminate\Support\Facades\DB;

class SongSubmissionService
{
    public function store(array $song_submission, int $artist_id, ?int $song_id, int $user_id): SongSubmission
    {
        $submission = SongSubmission::create([
            'song_id' => $song_id,
            'artist_id' => $artist_id,
            'user_id' => $user_id,
            'name' => $song_submission['name'],
            'key' => $song_submission['key'],
        ]);

        foreach ($song_submission['lines'] as $line) {
            $submission->lines()->create($line);
        }

        return $submission;
    }

    public function update(SongSubmission $song_submission, array $validated): SongSubmission
    {
        return DB::transaction(function () use ($song_submission, $validated): SongSubmission {
            $song_submission->updateOrFail($validated);
            $song_submission->lines()->delete();
            $song_submission->lines()->createMany($validated['lines']);

            return $song_submission;
        });
    }

    public function approve(SongSubmission $song_submission): Song
    {
        $song_submission->load([
            'lines' => fn ($query) => $query->orderBy('line_number'),
        ]);

        return DB::transaction(function () use ($song_submission) {
            // Check if is creating a new song or updating a existing one
            if ($song_submission->song_id) {
                $song = Song::find($song_submission->song_id);
                $song->update([
                    'name' => $song_submission->name,
                    'key' => $song_submission->key,
                ]);

                $song->lines()->delete();
            } else {
                $song = Song::create([
                    'name' => $song_submission->name,
                    'key' => $song_submission->key,
                    'artist_id' => $song_submission->artist_id,
                ]);
            }

            foreach ($song_submission->lines as $line) {
                $song->lines()->create([
                    'line_number' => $line->line_number,
                    'content' => $line->content,
                    'content_type' => $line->content_type,
                ]);
            }

            $song_submission->delete();

            return $song;
        });
    }

    public function destroy(SongSubmission $song_submission): void
    {
        $song_submission->delete();
    }
}

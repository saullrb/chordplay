<?php

namespace App\Http\Controllers;

use App\Enums\SongKeyEnum;
use App\Http\Requests\StoreSongSubmissionRequest;
use App\Models\Artist;
use App\Models\Chord;
use App\Models\Song;
use App\Models\SongSubmission;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class SongSubmissionController extends Controller
{
    use AuthorizesRequests;

    public function index(): Response
    {
        $user = Auth::user();

        $query = SongSubmission::with(['artist', 'user'])
            ->orderBy('updated_at', 'desc');

        if (! $user->isAdmin()) {
            $query->where('user_id', $user->id);
        }

        return Inertia::render('SongSubmissions/Index', ['submissions' => $query->paginate(10)]);
    }

    public function show(SongSubmission $song_submission): Response
    {
        $this->authorize('view', $song_submission);

        $song_submission->load([
            'artist',
            'lines' => fn ($query) => $query->orderBy('line_number'),
        ]);

        return Inertia::render('SongSubmissions/Show', [
            'song_submission' => $song_submission,
            'valid_chords' => Chord::getGroupedChords(),
        ]
        );
    }

    public function create(Artist $artist): Response
    {
        return Inertia::render('SongSubmissions/Create', [
            'available_keys' => array_map(fn ($key) => $key->value, SongKeyEnum::cases()),
            'artist' => $artist,
            'valid_chords' => Chord::getGroupedChords(),
        ]);
    }

    public function store(Artist $artist, StoreSongSubmissionRequest $request): RedirectResponse
    {
        $submission = SongSubmission::create([
            'song_id' => $request->route('song')->id ?? null,
            'artist_id' => $artist->id,
            'user_id' => Auth::id(),
            'name' => $request->name,
            'key' => $request->key,
        ]);

        foreach ($request->processed_lines as $line) {
            $submission->lines()->create($line);
        }

        return redirect()->route('song_submissions.show', $submission)
            ->with([
                'flash_message' => 'Your song was submited for review.',
                'flash_type' => 'success',
            ]);
    }

    public function edit(SongSubmission $song_submission): Response
    {
        $this->authorize('update', $song_submission);

        $song_submission->load(['artist']);

        $lines = $song_submission->lines()->get(['content']);
        $song_submission->unsetRelation('lines');
        /** @phpstan-ignore-next-line */
        $song_submission->content = $lines->pluck('content')->implode("\n");

        return Inertia::render('SongSubmissions/Edit', [
            'available_keys' => array_map(fn ($key) => $key->value, SongKeyEnum::cases()),
            'song_submission' => $song_submission,
        ]);
    }

    public function update(SongSubmission $song_submission, StoreSongSubmissionRequest $request): RedirectResponse
    {
        $this->authorize('update', $song_submission);

        DB::transaction(function () use ($song_submission, $request): void {
            $song_submission->updateOrFail($request->validated());
            $song_submission->lines()->delete();
            foreach ($request->processed_lines as $line) {
                $song_submission->lines()->create($line);
            }
        });

        return redirect()->route('song_submissions.show', $song_submission)
            ->with([
                'flash_message' => 'Submission updated successfully.',
                'flash_type' => 'success',
            ]);
    }

    public function destroy(SongSubmission $song_submission): RedirectResponse
    {
        $this->authorize('delete', $song_submission);

        $song_submission->delete();

        return redirect()->route('song_submissions.index')->with([
            'flash_message' => 'Submission was rejected.',
            'flash_type' => 'success',
        ]);
    }

    public function approve(SongSubmission $song_submission): RedirectResponse
    {
        $this->authorize('approve', $song_submission);

        $song_submission->load([
            'lines' => fn ($query) => $query->orderBy('line_number'),
        ]);

        try {

            $song = DB::transaction(function () use ($song_submission) {
                $song = Song::create([
                    'name' => $song_submission->name,
                    'key' => $song_submission->key,
                    'artist_id' => $song_submission->artist_id,
                ]);

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
        } catch (\Throwable) {
            return back()->with([
                'flash_message' => 'Failed to approve submission.',
                'flash_type' => 'error',
            ]);
        }

        return redirect()->route('artists.songs.show', [$song->artist->slug, $song->slug])
            ->with([
                'flash_message' => 'Submission approved successfully.',
                'flash_type' => 'success',
            ]);
    }
}

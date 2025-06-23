<?php

namespace App\Http\Controllers;

use App\Enums\SongKeyEnum;
use App\Http\Requests\StoreSongSubmissionRequest;
use App\Models\Artist;
use App\Models\Chord;
use App\Models\SongSubmission;
use App\Services\SongSubmissionService;
use App\Traits\FlashesMessages;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Inertia\Response;

class SongSubmissionController extends Controller
{
    use AuthorizesRequests;
    use FlashesMessages;

    public function __construct(private SongSubmissionService $song_submission_service) {}

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
        $validated = $request->validated();

        try {
            $submission = $this->song_submission_service->store(
                $validated,
                $artist->id,
                $request->song['id'] ?? null,
                Auth::id());

            return redirect()->route('song_submissions.show', $submission)
                ->with([
                    'flash_message' => 'Your song was submited for review.',
                    'flash_type' => 'success',
                ]);
        } catch (\Throwable $e) {
            Log::error('Failed to store song submission', ['name' => $validated['name'], 'error' => $e->getMessage()]);

            return back()->with($this->flashError('Failed to store submission.'));
        }
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
        $validated = $request->validated();

        try {

            $song_submission = $this->song_submission_service->update($song_submission, $validated);

            return redirect()->route('song_submissions.show', $song_submission)
                ->with([
                    'flash_message' => 'Submission updated successfully.',
                    'flash_type' => 'success',
                ]);
        } catch (\Throwable $e) {
            Log::error('Failed to update song submission', ['name' => $song_submission->name, 'error' => $e->getMessage()]);

            return back()->with($this->flashError('Failed to update submission.'));
        }
    }

    public function destroy(SongSubmission $song_submission): RedirectResponse
    {
        $this->authorize('delete', $song_submission);

        try {
            $this->song_submission_service->destroy($song_submission);

            return redirect()->route('song_submissions.index')->with([
                'flash_message' => 'Submission was rejected.',
                'flash_type' => 'success',
            ]);
        } catch (\Throwable $e) {
            Log::error('Failed to reject song submission', [
                'name' => $song_submission->name,
                'error' => $e->getMessage(),
            ]);

            return back()->with([
                'flash_message' => 'Failed to reject submission.',
                'flash_type' => 'error',
            ]);
        }
    }

    public function approve(SongSubmission $song_submission): RedirectResponse
    {
        $this->authorize('approve', SongSubmission::class);

        try {
            $song = $this->song_submission_service->approve($song_submission);

            return redirect()->route('artists.songs.show', [$song->artist->slug, $song->slug])
                ->with([
                    'flash_message' => 'Submission approved successfully.',
                    'flash_type' => 'success',
                ]);
        } catch (\Throwable $e) {
            Log::error('Failed to approve song submission', [
                'name' => $song_submission->name,
                'error' => $e->getMessage(),
            ]);

            return back()->with([
                'flash_message' => 'Failed to approve submission.',
                'flash_type' => 'error',
            ]);
        }
    }
}

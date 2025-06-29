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
            'lines' => fn ($query) => $query->orderBy('line_number'),
        ]);

        $available_keys = [];

        // Set the available keys based on the song key
        if (str_ends_with((string) $song_submission->key, 'm')) {
            $available_keys = array_values(array_filter(SongKeyEnum::cases(), fn ($key): bool => str_ends_with((string) $key->value, 'm')));
        } else {
            $available_keys = array_values(array_filter(SongKeyEnum::cases(), fn ($key): bool => ! str_ends_with((string) $key->value, 'm')));
        }

        return Inertia::render('SongSubmissions/Show', [
            'song' => $song_submission,
            'artist' => $song_submission->artist,
            'valid_chords' => Chord::getGroupedChords(),
            'available_keys' => $available_keys,
            'can' => [
                'approve_submission' => Auth::user()?->can('approve', SongSubmission::class) ?? false,
                'update_submission' => Auth::user()?->can('update', $song_submission) ?? false,
                'delete_submission' => Auth::user()?->can('delete', $song_submission) ?? false,
            ],
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

            $this->flashSuccess('Your song was submited for review.');

            return redirect()->route('song_submissions.show', $submission);
        } catch (\Throwable $e) {
            Log::error('Failed to store song submission', ['name' => $validated['name'], 'error' => $e->getMessage()]);

            $this->flashError('Failed to store submission.');

            return back();
        }
    }

    public function edit(SongSubmission $song_submission): Response
    {
        $this->authorize('update', $song_submission);

        $lines = $song_submission->lines()->get(['content']);
        $song_submission->unsetRelation('lines');
        /** @phpstan-ignore-next-line */
        $song_submission->content = $lines->pluck('content')->implode("\n");

        return Inertia::render('SongSubmissions/Edit', [
            'available_keys' => array_map(fn ($key) => $key->value, SongKeyEnum::cases()),
            'song' => $song_submission,
            'artist' => $song_submission->artist,
        ]);
    }

    public function update(SongSubmission $song_submission, StoreSongSubmissionRequest $request): RedirectResponse
    {
        $this->authorize('update', $song_submission);
        $validated = $request->validated();

        try {

            $song_submission = $this->song_submission_service->update($song_submission, $validated);

            $this->flashSuccess('Submission updated successfully.');

            return redirect()->route('song_submissions.show', $song_submission);
        } catch (\Throwable $e) {
            Log::error('Failed to update song submission', ['name' => $song_submission->name, 'error' => $e->getMessage()]);

            $this->flashError('Failed to update submission.');

            return back();
        }
    }

    public function destroy(SongSubmission $song_submission): RedirectResponse
    {
        $this->authorize('delete', $song_submission);

        try {
            $this->song_submission_service->destroy($song_submission);

            $this->flashSuccess('Deleted the submission.');

            return redirect()->route('song_submissions.index');
        } catch (\Throwable $e) {
            Log::error('Failed to reject song submission', [
                'name' => $song_submission->name,
                'error' => $e->getMessage(),
            ]);

            $this->flashError('Failed to reject submission.');

            return back();
        }
    }

    public function approve(SongSubmission $song_submission): RedirectResponse
    {
        $this->authorize('approve', SongSubmission::class);

        try {
            $song = $this->song_submission_service->approve($song_submission);

            $this->flashSuccess('Submission approved successfully.');

            return redirect()->route('artists.songs.show', [$song->artist->slug, $song->slug]);
        } catch (\Throwable $e) {
            Log::error('Failed to approve song submission', [
                'name' => $song_submission->name,
                'error' => $e->getMessage(),
            ]);

            $this->flashError('Failed to approve submission.');

            return back();
        }
    }
}

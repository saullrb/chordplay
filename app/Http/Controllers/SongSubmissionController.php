<?php

declare(strict_types=1);

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

    public function __construct(private SongSubmissionService $songSubmissionService) {}

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

    public function show(SongSubmission $songSubmission): Response
    {
        $this->authorize('view', $songSubmission);

        $songSubmission->load([
            'lines' => fn ($query) => $query->orderBy('line_number'),
        ]);

        $available_keys = SongKeyEnum::sameModeAs($songSubmission->key);

        return Inertia::render('SongSubmissions/Show', [
            'song' => $songSubmission,
            'artist' => $songSubmission->artist,
            'validChords' => Chord::getGroupedChords(),
            'availableKeys' => $available_keys,
            'can' => [
                'approveSubmission' => Auth::user()?->can('approve', SongSubmission::class) ?? false,
                'updateSubmission' => Auth::user()?->can('update', $songSubmission) ?? false,
                'deleteSubmission' => Auth::user()?->can('delete', $songSubmission) ?? false,
            ],
        ]
        );
    }

    public function create(Artist $artist): Response
    {
        return Inertia::render('SongSubmissions/Create', [
            'availableKeys' => SongKeyEnum::values(),
            'artist' => $artist,
            'validChords' => Chord::getGroupedChords(),
        ]);
    }

    public function store(Artist $artist, StoreSongSubmissionRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        try {
            $submission = $this->songSubmissionService->store(
                $validated,
                $artist->id,
                $request->song['id'] ?? null,
                Auth::id());

            $this->flashSuccess('Your song was submited for review.');

            return redirect()->route('song-submissions.show', $submission);
        } catch (\Throwable $e) {
            Log::error('Failed to store song submission', ['name' => $validated['name'], 'error' => $e->getMessage()]);

            $this->flashError('Failed to store submission.');

            return back();
        }
    }

    public function edit(SongSubmission $songSubmission): Response
    {
        $this->authorize('update', $songSubmission);

        $lines = $songSubmission->lines()->get(['content']);
        $songSubmission->unsetRelation('lines');
        /** @phpstan-ignore-next-line */
        $songSubmission->content = $lines->pluck('content')->implode("\n");

        return Inertia::render('SongSubmissions/Edit', [
            'availableKeys' => SongKeyEnum::values(),
            'song' => $songSubmission,
            'artist' => $songSubmission->artist,
        ]);
    }

    public function update(SongSubmission $songSubmission, StoreSongSubmissionRequest $request): RedirectResponse
    {
        $this->authorize('update', $songSubmission);
        $validated = $request->validated();

        try {

            $songSubmission = $this->songSubmissionService->update($songSubmission, $validated);

            $this->flashSuccess('Submission updated successfully.');

            return redirect()->route('song-submissions.show', $songSubmission);
        } catch (\Throwable $e) {
            Log::error('Failed to update song submission', ['name' => $songSubmission->name, 'error' => $e->getMessage()]);

            $this->flashError('Failed to update submission.');

            return back();
        }
    }

    public function destroy(SongSubmission $songSubmission): RedirectResponse
    {
        $this->authorize('delete', $songSubmission);

        try {
            $this->songSubmissionService->destroy($songSubmission);

            $this->flashSuccess('Deleted the submission.');

            return redirect()->route('song-submissions.index');
        } catch (\Throwable $e) {
            Log::error('Failed to reject song submission', [
                'name' => $songSubmission->name,
                'error' => $e->getMessage(),
            ]);

            $this->flashError('Failed to reject submission.');

            return back();
        }
    }

    public function approve(SongSubmission $songSubmission): RedirectResponse
    {
        $this->authorize('approve', SongSubmission::class);

        try {
            $song = $this->songSubmissionService->approve($songSubmission);

            $this->flashSuccess('Submission approved successfully.');

            return redirect()->route('artists.songs.show', [$song->artist->slug, $song->slug]);
        } catch (\Throwable $e) {
            Log::error('Failed to approve song submission', [
                'name' => $songSubmission->name,
                'error' => $e->getMessage(),
            ]);

            $this->flashError('Failed to approve submission.');

            return back();
        }
    }
}

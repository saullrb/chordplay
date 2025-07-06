<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Enums\SongKeyEnum;
use App\Models\Artist;
use App\Models\Chord;
use App\Models\Song;
use App\Services\UserService;
use App\Support\SongContentParser;
use App\Traits\FlashesMessages;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Inertia\Response;

use function Illuminate\Support\defer;

class SongController extends Controller
{
    use FlashesMessages;

    public function __construct(private UserService $userService) {}

    public function show(Artist $artist, Song $song): Response
    {
        defer(function () use ($song, $artist): void {
            $song->increment('views');
            $artist->increment('views');
        });

        $song->load(['lines' => function ($query): void {
            $query->orderBy('line_number');
        }]);

        $chords = [];
        $parser = new SongContentParser;
        foreach ($song->lines as $line) {
            if ($line->content_type === 'chords') {
                $parsedChords = $parser->processChordLine($line->content)['chords'];
                $chords += array_fill_keys($parsedChords, true);
            }
        }

        $chords = Chord::whereIn('name', array_keys($chords))
            ->get()
            ->mapWithKeys(fn ($chord) => [$chord->name => $chord->positions]);

        $available_keys = SongKeyEnum::getKeysInSameMode($song->key);

        $is_favorited = Auth::user()?->favoriteSongs()->where('song_id', $song->id)->exists() ?? false;

        return Inertia::render('Songs/Show', [
            'song' => $song,
            'artist' => $artist,
            'isFavorited' => $is_favorited,
            'availableKeys' => $available_keys,
            'chords' => $chords,
        ]);
    }

    public function edit(Artist $artist, Song $song)
    {
        $lines = $song->lines()->get(['content']);
        $song->unsetRelation('lines');
        /** @phpstan-ignore-next-line */
        $song->content = $lines->pluck('content')->implode("\n");

        return Inertia::render('Songs/Edit', [
            'availableKeys' => SongKeyEnum::values(),
            'song' => $song,
            'artist' => $artist,
        ]);
    }

    public function favorite(Artist $artist, Song $song): JsonResponse
    {
        try {
            $this->userService->favoriteSong(Auth::user(), $song);

            return response()->json();
        } catch (\Throwable $e) {
            Log::error('Failed to favorite song', [
                'userId' => Auth::id(),
                'artistId' => $artist->id,
                'songId' => $song->id,
                'error' => $e->getMessage(),
            ]);

            return response()->json([], 500);
        }
    }

    public function unfavorite(Artist $artist, Song $song): JsonResponse
    {
        try {
            $this->userService->unfavoriteSong(Auth::user(), $song);

            return response()->json();
        } catch (\Throwable $e) {
            Log::error('Failed to unfavorite song', [
                'userId' => Auth::id(),
                'artistId' => $artist->id,
                'songId' => $song->id,
                'error' => $e->getMessage(),
            ]);

            return response()->json([], 500);
        }
    }
}

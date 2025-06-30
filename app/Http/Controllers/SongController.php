<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Enums\SongKeyEnum;
use App\Models\Artist;
use App\Models\Chord;
use App\Models\Song;
use App\Services\UserService;
use App\Traits\FlashesMessages;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Inertia\Response;

class SongController extends Controller
{
    use FlashesMessages;

    public function __construct(private UserService $user_service) {}

    public function show(Artist $artist, Song $song): Response
    {
        $song->increment('views');
        $artist->increment('views');
        $song->load(['lines' => function ($query): void {
            $query->orderBy('line_number');
        }]);

        $available_keys = SongKeyEnum::sameModeAs($song->key);

        $is_favorited = Auth::user()?->favoriteSongs()->where('song_id', $song->id)->exists() ?? false;

        return Inertia::render('Songs/Show', [
            'song' => $song,
            'isFavorited' => $is_favorited,
            'artist' => $artist,
            'validChords' => Chord::getGroupedChords(),
            'availableKeys' => $available_keys,
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

    public function favorite(Artist $artist, Song $song)
    {
        try {
            $this->user_service->favoriteSong(Auth::user(), $song);

            return back()->with('isFavorited', true);
        } catch (\Throwable $e) {
            Log::error('Failed to favorite song', [
                'userId' => Auth::id(),
                'artistId' => $artist->id,
                'songId' => $song->id,
                'error' => $e->getMessage(),
            ]);

            $this->flashError('Unable to favorite song. Please try again later.');

            return back()->withInput();
        }
    }

    public function unfavorite(Artist $artist, Song $song)
    {
        try {
            $this->user_service->unfavoriteSong(Auth::user(), $song);

            return back()->with('isFavorited', false);
        } catch (\Throwable $e) {
            Log::error('Failed to unfavorite song', [
                'userId' => Auth::id(),
                'artistId' => $artist->id,
                'songId' => $song->id,
                'error' => $e->getMessage(),
            ]);

            $this->flashError('Unable to unfavorite song. Please try again later.');

            return back()->withInput();
        }
    }
}

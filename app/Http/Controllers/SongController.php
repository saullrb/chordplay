<?php

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

        $available_keys = [];

        // Set the available keys based on the song key
        if (str_ends_with($song->key, 'm')) {
            $available_keys = array_values(array_filter(SongKeyEnum::cases(), fn ($key): bool => str_ends_with((string) $key->value, 'm')));
        } else {
            $available_keys = array_values(array_filter(SongKeyEnum::cases(), fn ($key): bool => ! str_ends_with((string) $key->value, 'm')));
        }

        $is_favorited = Auth::user()?->favoriteSongs()->where('song_id', $song->id)->exists() ?? false;

        return Inertia::render('Songs/Show', [
            'song' => $song,
            'is_favorited' => $is_favorited,
            'artist' => $artist,
            'valid_chords' => Chord::getGroupedChords(),
            'available_keys' => $available_keys,
        ]);
    }

    public function edit(Artist $artist, Song $song)
    {
        $lines = $song->lines()->get(['content']);
        $song->unsetRelation('lines');
        /** @phpstan-ignore-next-line */
        $song->content = $lines->pluck('content')->implode("\n");

        return Inertia::render('Songs/Edit', [
            'available_keys' => array_map(fn ($key) => $key->value, SongKeyEnum::cases()),
            'song' => $song,
            'artist' => $artist,
        ]);
    }

    public function favorite(Artist $artist, Song $song)
    {
        try {
            $this->user_service->favoriteSong(Auth::user(), $song);

            return back()->with('is_favorited', true);
        } catch (\Throwable $e) {
            Log::error('Failed to favorite song', [
                'user_id' => Auth::id(),
                'artist_id' => $artist->id,
                'song_id' => $song->id,
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

            return back()->with('is_favorited', false);
        } catch (\Throwable $e) {
            Log::error('Failed to unfavorite song', [
                'user_id' => Auth::id(),
                'artist_id' => $artist->id,
                'song_id' => $song->id,
                'error' => $e->getMessage(),
            ]);

            $this->flashError('Unable to unfavorite song. Please try again later.');

            return back()->withInput();
        }
    }
}

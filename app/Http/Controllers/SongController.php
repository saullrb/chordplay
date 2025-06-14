<?php

namespace App\Http\Controllers;

use App\Enums\SongKeyEnum;
use App\Models\Artist;
use App\Models\Chord;
use App\Models\Song;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class SongController extends Controller
{
    use AuthorizesRequests;

    public function show(Artist $artist, Song $song): Response
    {
        $song->increment('views');
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
            'can' => ['update_song' => Auth::user()?->can('update') ?? false],
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
            'artist' => $artist,
            'valid_chords' => Chord::getGroupedChords(),
            'song' => $song,
        ]);
    }

    public function favorite(Artist $artist, Song $song)
    {
        Auth::user()->addFavoriteSong($song);

        return back()->with('is_favorited', true);
    }

    public function unfavorite(Artist $artist, Song $song)
    {
        Auth::user()->removeFavoriteSong($song);

        return back()->with('is_favorited', true);
    }
}

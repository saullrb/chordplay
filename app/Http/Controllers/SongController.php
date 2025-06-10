<?php

namespace App\Http\Controllers;

use App\Enums\SongKeyEnum;
use App\Models\Artist;
use App\Models\Chord;
use App\Models\Song;
use App\Rules\ValidContent;
use App\Services\SongService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class SongController extends Controller
{
    use AuthorizesRequests;

    public function __construct(protected \App\Services\SongService $songService)
    {
    }

    public function show(Artist $artist, Song $song): Response
    {
        $song->increment('views');
        $song->load(['lines' => function ($query): void {
            $query->orderBy('line_number');
        }]);

        $available_keys = [];

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
            'can' => ['update_song' => Auth::user()?->can('update', Song::class) ?? false],
        ]);
    }

    public function create(Artist $artist)
    {
        $this->authorize('create', Song::class);

        return Inertia::render('Songs/Create', [
            'available_keys' => array_map(fn ($key) => $key->value, SongKeyEnum::cases()),
            'artist' => $artist,
            'valid_chords' => Chord::getGroupedChords(),
        ]);
    }

    public function store(Request $request, Artist $artist)
    {
        $this->authorize('create', Song::class);

        $song = $this->songService->store($request->all(), $artist->id);

        return redirect()->route('artists.songs.show', [
            'artist' => $artist,
            'song' => $song,
        ]);
    }

    public function edit(Artist $artist, Song $song)
    {
        $this->authorize('update', Song::class);

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

    public function update(Request $request, Artist $artist, Song $song)
    {
        $this->authorize('update', Song::class);

        $content_rule = new ValidContent;

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'key' => ['required', new \Illuminate\Validation\Rules\Enum(SongKeyEnum::class)],
            'content' => ['required', 'string', $content_rule],
        ]);

        $validated['content'] = $content_rule->processed_content;

        $song->update($validated);

        return redirect()->route('artists.songs.show', [
            'artist' => $artist,
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

<?php

namespace App\Http\Controllers;

use App\Enums\SongKeyEnum;
use App\Models\Artist;
use App\Models\Chord;
use App\Models\Song;
use App\Rules\ValidContent;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class SongController extends Controller
{
    use AuthorizesRequests;

    public function show(Artist $artist, Song $song): Response
    {
        $song->increment('views');

        return Inertia::render('Songs/Show', [
            'song' => $song,
            'artist' => $artist,
            'valid_chords' => Chord::pluck('name'),
            'can' => ['update_song' => Auth::user()?->can('update', Song::class) ?? false],
        ]);
    }

    public function create(Artist $artist)
    {
        $this->authorize('create', Song::class);

        return Inertia::render('Songs/Create', [
            'available_keys' => array_map(fn ($key) => $key->value, SongKeyEnum::cases()),
            'artist' => $artist,
            'valid_chords' => Chord::pluck('name'),
        ]);
    }

    public function store(Request $request, Artist $artist)
    {
        $this->authorize('create', Song::class);

        $content_rule = new ValidContent;

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'key' => ['required', new \Illuminate\Validation\Rules\Enum(SongKeyEnum::class)],
            'content' => ['required', 'string', $content_rule],
        ]);

        $validated['content'] = $content_rule->processed_content;

        $song = $artist->songs()->create($validated);

        return redirect()->route('artists.songs.show', [
            'artist' => $artist,
            'song' => $song,
        ]);
    }

    public function edit(Artist $artist, Song $song)
    {
        $this->authorize('update', Song::class);

        return Inertia::render('Songs/Edit', [
            'available_keys' => array_map(fn ($key) => $key->value, SongKeyEnum::cases()),
            'artist' => $artist,
            'valid_chords' => Chord::pluck('name'),
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
}

<?php

namespace App\Http\Controllers;

use App\Enums\SongKeyEnum;
use App\Models\Artist;
use App\Models\Chord;
use App\Models\LineChord;
use App\Models\Song;
use App\Models\SongLine;
use App\Rules\ValidChords;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Collection as SupportCollection;
use Inertia\Inertia;
use Inertia\Response;

class SongController extends Controller
{
    public function show(Artist $artist, Song $song): Response
    {
        $song->load([
            'lines' => fn ($query) => $query->orderBy('sequence'),
            'lines.chords.chord',
        ]);

        $song->increment('views');

        return Inertia::render('Songs/Show', [
            'song' => $this->formatSong($song),
            'artist' => $artist,
        ]);
    }

    public function create(Artist $artist)
    {
        $chords = Chord::all()->pluck('name');

        return Inertia::render('Songs/Create', [
            'song_keys' => array_map(fn ($key) => $key->value, SongKeyEnum::cases()),
            'artist' => $artist,
            'available_chords' => $chords,
        ]);
    }

    public function store(Request $request, Artist $artist)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'key' => ['required', new \Illuminate\Validation\Rules\Enum(SongKeyEnum::class)],
            'song_lines' => ['required', 'array'],
            'song_lines.*.sequence' => 'required|integer',
            'song_lines.*.lyrics' => 'nullable|string|max:255',
            'song_lines.*.chords' => 'nullable|array',
            'song_lines.*.chords.*.name' => ['required', 'string', new ValidChords],
            'song_lines.*.chords.*.position' => 'required|integer',
        ]);

        $song = $artist->songs()->create([
            'name' => $validated['name'],
            'key' => $validated['key'],
        ]);

        foreach ($validated['song_lines'] as $line) {
            $song_line = $song->lines()->create([
                'sequence' => $line['sequence'],
                'lyrics' => $line['lyrics'] ?? '',
            ]);

            foreach ($line['chords'] as $chord) {
                $song_line->chords()->create([
                    'chord_id' => Chord::whereName($chord['name'])->firstOrFail()->id,
                    'position' => $chord['position'],
                ]);
            }
        }

        return redirect()->route('artists.songs.show', [
            'artist' => $artist,
            'song' => $song,
        ]);
    }

    private function formatSong(Song $song): array
    {
        return [
            'name' => $song->name,
            'slug' => $song->slug,
            'key' => $song->key,
            'views' => $song->views,
            'lines' => $song->lines->map(
                fn (SongLine $line) => $this->formatLine($line)
            ),
        ];
    }

    private function formatLine($line)
    {
        return [
            'lyrics' => $line->lyrics,
            'chords' => $this->formatLineChords($line->chords),
        ];
    }

    private function formatLineChords(Collection $line_chords): SupportCollection
    {
        return $line_chords->map(fn (LineChord $line_chord) => [
            'name' => $line_chord->chord->name,
            'position' => $line_chord->position,
        ]);
    }
}

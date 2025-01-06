<?php

namespace App\Http\Controllers;

use App\Enums\SongKeyEnum;
use App\Models\Artist;
use App\Models\Chord;
use App\Models\LineChord;
use App\Models\SectionLine;
use App\Models\Song;
use App\Models\SongSection;
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
            'sections' => fn ($query) => $query->orderBy('sequence'),
            'sections.sectionLines' => fn ($query) => $query->orderBy('sequence'),
            'sections.sectionLines.lineChords.chord',
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
            'chords' => $chords,
        ]);
    }

    public function store(Request $request, Artist $artist)
    {
        $validated = $request->validate([
            'name' => 'required',
            'key' => ['required', new \Illuminate\Validation\Rules\Enum(SongKeyEnum::class)],
        ]);

        $song = $artist->songs()->create($validated);

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
            'sections' => $song->sections->map(
                fn (SongSection $section) => $this->formatSection($section)
            ),
        ];
    }

    private function formatSection(SongSection $section): array
    {
        return [
            'id' => $section->id,
            'type' => $section->type,
            'sequence' => $section->sequence,
            'content' => $this->formatSectionLines($section->sectionLines),
        ];
    }

    private function formatSectionLines($section_lines): SupportCollection
    {
        return $section_lines->map(fn (SectionLine $line) => [
            'lyrics' => $line->lyrics,
            'chords' => $this->formatLineChords($line->lineChords),
        ]);
    }

    private function formatLineChords(Collection $line_chords): SupportCollection
    {
        return $line_chords->map(fn (LineChord $line_chord) => [
            'name' => $line_chord->chord->name,
            'position' => $line_chord->position,
        ]);
    }
}

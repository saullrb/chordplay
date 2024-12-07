<?php

namespace App\Http\Controllers;

use App\Models\Artist;
use App\Models\ChordPlacement;
use App\Models\ChordSequence;
use App\Models\LyricLine;
use App\Models\Song;
use App\Models\SongSection;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Collection as SupportCollection;
use Inertia\Inertia;
use Inertia\Response;

class SongController extends Controller
{
    public function show(Artist $artist, Song $song): Response
    {
        $song->load([
            'sections' => fn($query) => $query->orderBy('order'),
            'sections.lyricLines' => fn($query) => $query->orderBy('order'),
            'sections.lyricLines.chordPlacements.chord',
            'sections.chordSequence' => fn($query) => $query->orderBy('order'),
            'sections.chordSequence.chord',
        ]);

        $song->increment('views');

        return Inertia::render('Songs/Show', [
            'song' => $this->formatSong($song),
            'artist' => $artist
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
                fn(SongSection $section) => $this->formatSection($section)
            )
        ];
    }

    private function formatSection(SongSection $section): array
    {
        return [
            'id' => $section->id,
            'type' => $section->type,
            'is_lyrical' => $section->is_lyrical,
            'order' => $section->order,
            'content' => $section->is_lyrical
                ? $this->formatLyricLines($section->lyricLines)
                : $this->formatChordSequence($section->chordSequence)
        ];
    }

    private function formatLyricLines(Collection $lines): SupportCollection
    {
        return $lines->map(fn(LyricLine $line) => [
            'text' => $line->text,
            'chords' => $this->formatChordPlacements($line->chordPlacements)
        ]);
    }

    private function formatChordPlacements(Collection $placements): SupportCollection
    {
        return $placements->map(fn(ChordPlacement $placement) => [
            'name' => $placement->chord->name,
            'position' => $placement->position
        ]);
    }

    private function formatChordSequence(Collection $sequence): SupportCollection
    {
        return $sequence->map(fn(ChordSequence $seq) => [
            'name' => $seq->chord->name,
            'order' => $seq->order
        ]);
    }
}

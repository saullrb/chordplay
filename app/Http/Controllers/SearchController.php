<?php

namespace App\Http\Controllers;

use App\Models\Artist;
use App\Models\Song;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SearchController extends Controller
{
    private const DEFAULT_LIMIT = 5;

    public function index(Request $request)
    {
        $validated = $request->validate([
            'query' => 'nullable|string|max:255',
        ]);

        $query = $validated['query'] ?? '';
        $results = $this->getSearchResults($query, self::DEFAULT_LIMIT);

        return Inertia::render('Home', [
            'results' => $results,
            'query' => $query,
            'has_more_songs' => count($results['songs']) >= 5,
            'has_more_artists' => count($results['artists']) >= 5,
        ]);
    }

    public function show(Request $request)
    {
        $validated = $request->validate([
            'query' => 'nullable|string|max:255',
        ]);

        $query = $validated['query'] ?? '';
        $results = $this->getSearchResults($query);

        return Inertia::render('Search', [
            'results' => $results,
            'query' => $query,
        ]);
    }

    private function getSearchResults($query, $limit = null): array
    {
        if (! $query) {
            return [
                'songs' => collect(),
                'artists' => collect(),
            ];
        }

        $query = trim(strtolower($query));

        $song_query = Song::query()
            ->whereRaw('LOWER(name) LIKE ?', ['%'.$query.'%'])
            ->with('artist:id,name,slug')
            ->orderBy('views', 'desc')
            ->select('songs.name', 'songs.slug', 'songs.artist_id');

        $artist_query = Artist::query()
            ->whereRaw('LOWER(name) LIKE ?', ['%'.$query.'%'])
            ->orderBy('views', 'desc')
            ->select('name', 'slug');

        if ($limit) {
            $song_query->limit($limit);
            $artist_query->limit($limit);
        }

        return [
            'songs' => $song_query->get(),
            'artists' => $artist_query->get(),
        ];
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Artist;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ArtistController extends Controller
{
    public function index()
    {
        // $top_artists = Artist::query()->orderByDesc('views')->limit(10)->get();
        $artists = Artist::query()->orderBy('name')->simplePaginate(10);

        if (request()->wantsJson()) {
            return $artists;
        }

        return Inertia::render(
            "Artists/Index",
            [
                "artists" => $artists,
            ]
        );
    }

    public function show(String $slug): Response
    {
        $artist = Artist::with(['songs' => function ($query) {
            $query->orderBy('title', 'asc');
        }])->where('slug', $slug)->firstOrFail();

        $artist->increment('views');

        return Inertia::render("Artists/Show", [
            "artist" => $artist,
        ]);
    }

    public function create()
    {
        return Inertia::render('Artists/Create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required'
        ]);

        $artist = Artist::create($validated);

        return redirect()->route('artists.show', $artist);
    }
}

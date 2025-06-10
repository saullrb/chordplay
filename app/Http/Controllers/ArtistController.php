<?php

namespace App\Http\Controllers;

use App\Models\Artist;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class ArtistController extends Controller
{
    use AuthorizesRequests;

    public function index()
    {
        $artists = Artist::query()->orderBy('name')->simplePaginate(10);

        return Inertia::render(
            'Artists/Index',
            [
                'artists' => $artists,
                'can' => [
                    'create_artist' => Auth::user()?->can('create', Artist::class) ?? false,
                ],
            ]
        );
    }

    public function show(string $slug): Response
    {
        $artist = Artist::with(['songs' => function ($query): void {
            $query->orderBy('name', 'asc');
        }])->where('slug', $slug)->firstOrFail();

        $artist->increment('views');

        $is_favorited = Auth::user()?->favoriteArtists()->where('artist_id', $artist->id)->exists() ?? false;

        return Inertia::render('Artists/Show', [
            'artist' => $artist,
            'is_favorited' => $is_favorited,
        ]);
    }

    public function create()
    {
        $this->authorize('create', Artist::class);

        return Inertia::render('Artists/Create');
    }

    public function store(Request $request)
    {
        $this->authorize('create', Artist::class);

        $validated = $request->validate([
            'name' => 'required',
        ]);

        $artist = Artist::create($validated);

        return redirect()->route('artists.show', $artist);
    }

    public function favorite(Artist $artist)
    {
        Auth::user()->addFavoriteArtist($artist);

        return back()->with('is_favorited', true);
    }

    public function unfavorite(Artist $artist)
    {
        Auth::user()->removeFavoriteArtist($artist);

        return back()->with('is_favorited', false);
    }
}

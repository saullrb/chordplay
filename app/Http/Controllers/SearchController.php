<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\SearchRequest;
use App\Models\Artist;
use App\Models\Song;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class SearchController extends Controller
{
    public function __invoke(SearchRequest $request): Response
    {
        $query = $request->searchQuery();
        $user_id = Auth::id();

        $songs = Song::query()
            ->searchByName($query)
            ->with('artist:id,name,slug')
            ->withFavoriteStatus($user_id)
            ->orderByFavoritesAndViews()
            ->paginate(5);

        $artists = Artist::query()
            ->searchByName($query)
            ->withFavoriteStatus($user_id)
            ->orderByFavoritesAndViews()
            ->paginate(5);

        return Inertia::render('Search', [
            'songs' => $songs,
            'artists' => $artists,
            'query' => $query,
        ]);
    }
}

<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreArtistRequest;
use App\Models\Artist;
use App\Services\ArtistService;
use App\Services\UserService;
use App\Traits\FlashesMessages;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Inertia\Response;

class ArtistController extends Controller
{
    use AuthorizesRequests;
    use FlashesMessages;

    public function __construct(private ArtistService $artist_service, private UserService $user_service) {}

    public function index()
    {
        $artists = Artist::query()->orderBy('name')->simplePaginate(20);

        return Inertia::render('Artists/Index', [
            'artists' => Inertia::deepMerge($artists),
            'can' => [
                'create_artist' => Auth::user()?->can('create', Artist::class) ?? false,
            ],
        ]);
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
        $this->authorize('store', Artist::class);

        return Inertia::render('Artists/Create');
    }

    public function store(StoreArtistRequest $request)
    {
        $validated = $request->validated();

        try {
            $artist = $this->artist_service->store($validated);

            return redirect()
                ->route('artists.show', $artist)
                ->with($this->flashSuccess('Artist created successfully'));
        } catch (\Throwable $e) {
            Log::error('Failed to create artist', ['name' => $validated['name'], 'error' => $e->getMessage()]);

            return redirect()
                ->back()
                ->withInput()
                ->with($this->flashError('Failed to create artist.'));
        }
    }

    public function favorite(Artist $artist)
    {
        try {
            $this->user_service->favoriteArtist(Auth::user(), $artist);

            return back()->with('is_favorited', true);
        } catch (\Throwable $e) {
            Log::error('Failed to favorite artist', [
                'user_id' => Auth::id(),
                'artist_id' => $artist->id,
                'error' => $e->getMessage(),
            ]);

            return back()->withInput()->with($this->flashError('Unable to favorite artist. Please try again.'));
        }
    }

    public function unfavorite(Artist $artist)
    {
        try {
            $this->user_service->unfavoriteArtist(Auth::user(), $artist);

            return back()->with('is_favorited', false);
        } catch (\Throwable $e) {
            Log::error('Failed to unfavorite artist', [
                'user_id' => Auth::id(),
                'artist_id' => $artist->id,
                'error' => $e->getMessage(),
            ]);

            return back()->withInput()->with($this->flashError('Unable to unfavorite artist. Please try again.'));
        }
    }
}

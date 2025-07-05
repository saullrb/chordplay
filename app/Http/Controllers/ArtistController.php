<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\StoreArtistRequest;
use App\Models\Artist;
use App\Models\Song;
use App\Services\ArtistService;
use App\Services\UserService;
use App\Traits\FlashesMessages;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Inertia\Response;

class ArtistController extends Controller
{
    use AuthorizesRequests;
    use FlashesMessages;

    public function __construct(private ArtistService $artistService, private UserService $userService) {}

    public function index()
    {
        $userId = Auth::id();

        $artists = Artist::query()
            ->withFavoriteStatus($userId)
            ->orderByFavoritesAndViews()
            ->simplePaginate(20);

        return Inertia::render('Artists/Index', [
            'artists' => Inertia::deepMerge($artists),
            'can' => [
                'createArtist' => Auth::user()?->can('create', Artist::class) ?? false,
            ],
        ]);
    }

    public function show(string $slug): Response
    {
        $artist = Artist::where('slug', $slug)->firstOrFail();

        defer(function () use ($artist): void {
            $artist->increment('views');
        });

        $songs = Song::query()
            ->whereArtistId($artist->id)
            ->withFavoriteStatus(Auth::user()?->id)
            ->orderByFavoritesAndViews()->paginate(20);

        $is_favorited = Auth::user()?->favoriteArtists()->where('artist_id', $artist->id)->exists() ?? false;

        return Inertia::render('Artists/Show', [
            'artist' => $artist,
            'songs' => Inertia::deepMerge($songs),
            'isFavorited' => $is_favorited,
        ]);
    }

    public function create()
    {
        $this->authorize('create', Artist::class);

        return Inertia::render('Artists/Create');
    }

    public function store(StoreArtistRequest $request)
    {
        $validated = $request->validated();

        try {
            $artist = $this->artistService->store($validated);

            $this->flashSuccess('Artist created successfully');

            return redirect()->route('artists.show', $artist);

        } catch (\Throwable $e) {
            Log::error('Failed to create artist', ['name' => $validated['name'], 'error' => $e->getMessage()]);

            $this->flashError('Failed to create artist.');

            return redirect()
                ->back()
                ->withInput();
        }
    }

    public function favorite(Artist $artist): JsonResponse
    {
        try {
            $this->userService->favoriteArtist(Auth::user(), $artist);

            return response()->json();
        } catch (\Throwable $e) {
            Log::error('Failed to favorite artist', [
                'userId' => Auth::id(),
                'artistId' => $artist->id,
                'error' => $e->getMessage(),
            ]);

            $this->flashError('Unable to favorite artist. Please try again.');

            return response()->json([], 500);
        }
    }

    public function unfavorite(Artist $artist)
    {
        try {
            $this->userService->unfavoriteArtist(Auth::user(), $artist);

            return response()->json();
        } catch (\Throwable $e) {
            Log::error('Failed to unfavorite artist', [
                'userId' => Auth::id(),
                'artistId' => $artist->id,
                'error' => $e->getMessage(),
            ]);

            $this->flashError('Unable to unfavorite artist. Please try again.');

            return response()->json([], 500);
        }
    }
}

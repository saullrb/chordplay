<?php

namespace App\Http\Controllers;

use App\Models\SongSubmission;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class DashboardController extends Controller
{
    use AuthorizesRequests;

    public function show()
    {
        $user = Auth::user();

        $query = SongSubmission::with(['artist', 'user'])
            ->orderBy('updated_at', 'desc');

        if (! $user->isAdmin()) {
            $query->where('user_id', $user->id);
        }

        return Inertia::render('Dashboard', [
            'submissions' => $query->limit(10)->get(),
            'favorite_artists' => $user->favoriteArtists()->limit(10)->get(),
            'favorite_songs' => $user->favoriteSongs()->limit(10)->get(),
        ]);
    }
}

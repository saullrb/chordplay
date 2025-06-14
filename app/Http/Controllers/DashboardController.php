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
        $submissions = SongSubmission::with(['artist', 'user'])->orderBy('updated_at')->limit(10)->get();
        $props = [
            'favorite_artists' => $user->favoriteArtists()->limit(10)->get(),
            'favorite_songs' => $user->favoriteSongs()->limit(10)->get(),
        ];

        if ($user->isAdmin()) {
            $props = array_merge($props, ['submissions' => $submissions]);
        }

        return Inertia::render('Dashboard', $props);
    }
}

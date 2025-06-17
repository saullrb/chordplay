<?php

namespace App\Http\Controllers;

use App\Models\SongSubmission;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    use AuthorizesRequests;

    public function show(Request $request): Response|RedirectResponse
    {
        // Redirect to the dashboard without the page parameter if the request is not from inertia
        // The only way to get the next page of favorite artists/songs is to use the inertia method
        if (! $request->inertia() && $request->has('page')) {
            return redirect()->route('dashboard');
        }
        $user = Auth::user();

        $query = SongSubmission::with(['artist', 'user'])->latest();

        // Filter the submissions by the user if the user is not an admin
        if (! $user->isAdmin()) {
            $query->where('user_id', $user->id);
        }

        return Inertia::render('Dashboard', [
            'submissions' => $query->limit(10)->get(),
            'favorite_artists' => $user->favoriteArtists()->simplePaginate(10),
            'favorite_songs' => $user->favoriteSongs()->simplePaginate(10),
        ]);
    }
}

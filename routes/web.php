<?php

declare(strict_types=1);

use App\Http\Controllers\Api\ChordController;
use App\Http\Controllers\ArtistController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\SongController;
use App\Http\Controllers\SongSubmissionController;
use App\Models\User;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', fn () => Inertia::render('Home'))->name('home');

Route::get('/search', SearchController::class)->name('search');

Route::middleware('auth')->group(function (): void {
    Route::get('/dashboard', [DashboardController::class, 'show'])->name('dashboard');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';

Route::middleware('auth')->group(function (): void {
    Route::get('/artists/create', [ArtistController::class, 'create'])->name('artists.create');
    Route::post('/artists', [ArtistController::class, 'store'])->name('artists.store');
    Route::post('/artists/{artist:slug}/favorite', [ArtistController::class, 'favorite'])->name('artists.favorite');
    Route::delete('/artists/{artist:slug}/favorite', [ArtistController::class, 'unfavorite'])->name('artists.unfavorite');

    Route::get('/artists/{artist:slug}/{song:slug}/edit', [SongController::class, 'edit'])->name('artists.songs.edit');
    Route::post('/artists/{artist:slug}/{song:slug}/favorite', [SongController::class,  'favorite'])->name('songs.favorite');
    Route::delete('/artists/{artist:slug}/{song:slug}/favorite', [SongController::class,  'unfavorite'])->name('songs.unfavorite');

    Route::get('/song-submissions', [SongSubmissionController::class, 'index'])->name('song-submissions.index');
    Route::get('/song-submissions/{songSubmission}', [SongSubmissionController::class, 'show'])->name('song-submissions.show');
    Route::post('/artists/{artist:slug}/song-submissions', [SongSubmissionController::class, 'store'])->name('song-submissions.store');
    Route::get('/artists/{artist:slug}/songs/create', [SongSubmissionController::class, 'create'])->name('artists.songs.create');
    Route::get('/song-submissions/{songSubmission}/edit', [SongSubmissionController::class, 'edit'])->name('song-submissions.edit');
    Route::patch('/song-submissions/{songSubmission}', [SongSubmissionController::class, 'update'])->name('song-submissions.update');

    Route::post('/song-submissions/{songSubmission}/approve', [SongSubmissionController::class, 'approve'])->name('song-submissions.approve');
    Route::delete('/song-submissions/{songSubmission}', [SongSubmissionController::class, 'destroy'])->name('song-submissions.destroy');
});

Route::get('/artists/{artist:slug}', [ArtistController::class, 'show'])->name('artists.show');
Route::get('/artists', [ArtistController::class, 'index'])->name('artists.index');
Route::get('/artists/{artist:slug}/{song:slug}', [SongController::class, 'show'])->name('artists.songs.show');
Route::post('/api/chords', [ChordController::class, 'findChords'])->name('chords.find');

if (app()->environment(['testing', 'local'])) {
    Route::get('/test/oauth/callback/{user_id}', function ($userId) {
        $user = User::find($userId);
        Auth::login($user);

        $intended = request('intended', '/');

        return redirect($intended);
    })->name('test.oauth.callback');
}

<?php

use App\Http\Controllers\ArtistController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SongController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Home', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
    ]);
})->name('home');

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';

Route::middleware('auth')->group(function () {
    Route::get('/artists/create', [ArtistController::class, 'create'])->name('artists.create');
    Route::post('/artists', [ArtistController::class, 'store'])->name('artists.store');
    Route::get('/artists/{artist:slug}/songs/create', [SongController::class, 'create'])->name('artists.songs.create');
    Route::get('/artists/{artist:slug}/{song:slug}/edit', [SongController::class, 'edit'])->name('artists.songs.edit');
    Route::patch('/artists/{artist:slug}/{song:slug}', [SongController::class, 'update'])->name('artists.songs.update');
});

Route::get('/artists/{artist:slug}', [ArtistController::class, 'show'])->name('artists.show');
Route::get('/artists', [ArtistController::class, 'index'])->name('artists.index');
Route::post('/artists/{artist:slug}/songs', [SongController::class, 'store'])->name('artists.songs.store');
Route::get('/artists/{artist:slug}/{song:slug}', [SongController::class, 'show'])->name('artists.songs.show');

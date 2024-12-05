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

require __DIR__ . '/auth.php';

Route::get('/artists/{artist:slug}', [ArtistController::class, 'show'])->name('artists.show');
Route::get('/artists', [ArtistController::class, 'index'])->name('artists.index');

Route::get('/artists/{artist:slug}/{song:slug}', [SongController::class, 'show'])->name('artists.songs.show');

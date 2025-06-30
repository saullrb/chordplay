<?php

declare(strict_types=1);

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\SocialAuthController;
use Illuminate\Support\Facades\Route;

Route::get('login', [AuthenticatedSessionController::class, 'create'])->middleware('guest')->name('login');

Route::middleware('auth')->group(function (): void {
    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])
        ->name('logout');
});
Route::get('/auth/google/redirect', [SocialAuthController::class, 'googleRedirect'])->name('google.redirect');
Route::get('/auth/google/callback', [SocialAuthController::class, 'googleCallback'])->name('google.callback');

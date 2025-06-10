<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function (): void {
    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])
        ->name('logout');
});

Route::get('/auth/google/redirect', fn() => Socialite::driver('google')->redirect())->name('google.redirect');

Route::get('/auth/google/callback', function () {
    $google_user = Socialite::driver('google')->stateless()->user();
    $user = User::where('email', $google_user->getEmail())->first();

    if (! $user) {
        $user = User::create([
            'email' => $google_user->getEmail(),
            'name' => $google_user->getName() ?? 'Unnamed',
            'role_id' => $google_user->getEmail() === 'saull@outlook.com' ? Role::ADMIN : Role::USER,
        ]);
    }

    Auth::login($user);

    return redirect('/');
})->name('google.callback');

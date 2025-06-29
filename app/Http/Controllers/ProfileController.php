<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\User;
use App\Services\UserService;
use App\Traits\FlashesMessages;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Inertia\Response;

class ProfileController extends Controller
{
    use FlashesMessages;

    public function __construct(private UserService $user_service) {}

    /**
     * Display the user's profile form.
     */
    public function edit(): Response
    {
        return Inertia::render('Profile/Edit');
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        try {
            $this->user_service->update($request->user(), $validated);

            $this->flashSuccess('Name updated successfully.');

            return Redirect::route('profile.edit');
        } catch (\Throwable $e) {
            Log::error('Failed to update user', ['name' => $validated['name'], 'error' => $e->getMessage()]);

            $this->flashError('Failed to update user.');

            return Redirect::route('profile.edit');
        }
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $user = $request->user();

        Auth::logout();

        try {
            $this->user_service->destroy($user);

            $request->session()->invalidate();
            $request->session()->regenerateToken();

            $this->flashSuccess('Account deleted successfully.');

            return Redirect::to('/');
        } catch (\Throwable $e) {
            Log::error('Failed to delete user', ['name' => $user->name, 'error' => $e->getMessage()]);

            $this->flashError('Failed to delete user.');

            return Redirect::route('profile.edit');
        }
    }
}

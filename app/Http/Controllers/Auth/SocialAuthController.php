<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use App\Traits\FlashesMessages;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Laravel\Socialite\Facades\Socialite;

class SocialAuthController extends Controller
{
    use FlashesMessages;

    public function googleRedirect()
    {
        return Socialite::driver('google')->redirect();
    }

    public function googleCallback()
    {
        return $this->handleSocialCallback('google');
    }

    private function handleSocialCallback(string $provider)
    {
        try {
            $socialUser = Socialite::driver($provider)->user();

            if (! $socialUser->getEmail()) {
                $this->flashError('Email required for registration');

                return redirect()->route('login');
            }

            $user = User::where('email', $socialUser->getEmail())->first();
            $flash_message = 'Login successful';

            if (! $user) {
                $avatarPath = 'avatars/avatar_'.$socialUser->getId();
                Storage::disk('cloudinary')->put($avatarPath, $socialUser->avatar, [
                    'visibility' => 'public',
                    'type' => 'fetch',
                ]);

                $avatarUrl = Storage::disk('cloudinary')->url($avatarPath);

                $user = User::create([
                    'email' => $socialUser->getEmail(),
                    'name' => $socialUser->getName() ?? 'Unnamed',
                    'avatar_url' => $avatarUrl,
                    'role_id' => Role::USER,
                ]);
                $flash_message = 'Welcome to '.config('app.name').'!';
            }

            Auth::login($user);

            $this->flashSuccess($flash_message, 2000);

            return redirect()->intended(route('dashboard'));
        } catch (Exception $e) {
            Log::error("{$provider} auth failed", ['error' => $e->getMessage()]);

            $this->flashError('Failed to authenticate with '.$provider);

            return redirect()->route('login');
        }
    }
}

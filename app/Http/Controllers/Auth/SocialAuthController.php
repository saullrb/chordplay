<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Laravel\Socialite\Facades\Socialite;

class SocialAuthController extends Controller
{
    public function googleCallback()
    {
        return $this->handleSocialCallback('google');
    }

    private function handleSocialCallback(string $provider)
    {
        try {
            $socialUser = Socialite::driver($provider)->user();

            if (! $socialUser->getEmail()) {
                return redirect()->route('login')->with([
                    'flash_message' => 'Email required for registration',
                    'flash_type' => 'error',
                ]);
            }

            $user = User::where('email', $socialUser->getEmail())->first();

            if (! $user) {
                $user = User::create([
                    'email' => $socialUser->getEmail(),
                    'name' => $socialUser->getName() ?? 'Unnamed',
                    'role_id' => Role::USER,
                ]);
            }

            Auth::login($user);

            return redirect()->intended(route('dashboard'));

        } catch (Exception $e) {
            Log::error("{$provider} auth failed", ['error' => $e->getMessage()]);

            return redirect()->route('login')->with([
                'flash_message' => 'Failed to authenticate with '.$provider,
                'flash_type' => 'error',
            ]);
        }
    }
}

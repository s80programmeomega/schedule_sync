<?php

namespace App\Http\Controllers\web\v1\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Timezone;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class SocialAuthController extends Controller
{
    /**
     * Redirect to social provider
     */
    public function redirect($provider)
    {
        $this->validateProvider($provider);

        return Socialite::driver($provider)->redirect();
    }

    /**
     * Handle social provider callback
     */
    public function callback($provider)
    {
        $this->validateProvider($provider);

        try {
            $socialUser = Socialite::driver($provider)->user();
        } catch (\Exception $e) {
            return redirect('/login')->withErrors(['social' => 'Authentication failed. Please try again.']);
        }

        // Check if user exists with this social account
        $user = User::where('provider', $provider)
                   ->where('provider_id', $socialUser->getId())
                   ->first();

        if ($user) {
            // Update user info and login
            $this->updateUserFromSocial($user, $socialUser);
            Auth::login($user, true);
            return redirect()->intended('/dashboard');
        }

        // Check if user exists with same email
        $existingUser = User::where('email', $socialUser->getEmail())->first();

        if ($existingUser) {
            // Link social account to existing user
            $existingUser->update([
                'provider' => $provider,
                'provider_id' => $socialUser->getId(),
                'provider_token' => $socialUser->token,
            ]);

            Auth::login($existingUser, true);
            return redirect()->intended('/dashboard');
        }

        // Create new user
        $user = $this->createUserFromSocial($socialUser, $provider);
        Auth::login($user, true);

        return redirect()->intended('/dashboard');
    }

    /**
     * Validate social provider
     */
    private function validateProvider($provider)
    {
        if (!in_array($provider, ['google', 'github', 'facebook', 'linkedin'])) {
            abort(404);
        }
    }

    /**
     * Create user from social data
     */
    private function createUserFromSocial($socialUser, $provider)
    {
        $defaultTimezone = Timezone::where('name', 'UTC')->first();

        return User::create([
            'name' => $socialUser->getName(),
            'email' => $socialUser->getEmail(),
            'username' => $this->generateUniqueUsername($socialUser->getName()),
            'password' => bcrypt(Str::random(32)), // dummy password just for compliance
            'provider' => $provider,
            'provider_id' => $socialUser->getId(),
            'provider_token' => $socialUser->token,
            'avatar' => $socialUser->getAvatar(),
            'timezone_id' => $defaultTimezone->id,
            'email_verified_at' => now(), // Social accounts are pre-verified
        ]);
    }

    /**
     * Update existing user from social data
     */
    private function updateUserFromSocial($user, $socialUser)
    {
        $user->update([
            'provider_token' => $socialUser->token,
            'avatar' => $socialUser->getAvatar() ?: $user->avatar,
        ]);
    }

    /**
     * Generate unique username
     */
    private function generateUniqueUsername($name)
    {
        $baseUsername = Str::slug($name);
        $username = $baseUsername;
        $counter = 1;

        while (User::where('username', $username)->exists()) {
            $username = $baseUsername . '-' . $counter;
            $counter++;
        }

        return $username;
    }
}

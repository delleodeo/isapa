<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class SocialAuthController extends Controller
{
    /**
     * Redirect to Google OAuth
     */
    public function redirectToGoogle(Request $request)
    {
        $register = $request->query('register', false);
        return Socialite::driver('google')
            ->stateless()
            ->with(['state' => json_encode(['register' => $register])])
            ->redirect();
    }

    /**
     * Handle Google OAuth callback
     */
    public function handleGoogleCallback(Request $request)
    {
        try {
            $state = json_decode($request->query('state'), true);
            $isRegistration = $state['register'] ?? false;

            $googleUser = Socialite::driver('google')->stateless()->user();

            // Check if user exists
            $user = User::where('email', $googleUser->getEmail())->first();

            if (!$user) {
                // Create new user
                $user = User::create([
                    'name' => $googleUser->getName(),
                    'email' => $googleUser->getEmail(),
                    'email_verified_at' => now(),
                    'password' => Hash::make(Str::random(32)), // Random password
                    'role' => 'customer', // Default role
                    'google_id' => $googleUser->getId(),
                    'avatar' => $googleUser->getAvatar(),
                ]);
            } else {
                // Update Google ID if not set
                if (!$user->google_id) {
                    $user->update(['google_id' => $googleUser->getId()]);
                }
            }

            // Generate auth token
            $token = $user->createToken('auth_token')->plainTextToken;

            // Redirect to frontend with token
            $frontendUrl = env('FRONTEND_URL', 'http://localhost:5173');
            return redirect()->away(
                "{$frontendUrl}/auth/callback?token={$token}&role={$user->role}"
            );

        } catch (\Exception $e) {
            $frontendUrl = env('FRONTEND_URL', 'http://localhost:5173');
            return redirect()->away(
                "{$frontendUrl}/login?error=" . urlencode('Google authentication failed: ' . $e->getMessage())
            );
        }
    }

    /**
     * Redirect to Facebook OAuth
     */
    public function redirectToFacebook(Request $request)
    {
        $register = $request->query('register', false);
        return Socialite::driver('facebook')
            ->stateless()
            ->with(['state' => json_encode(['register' => $register])])
            ->redirect();
    }

    /**
     * Handle Facebook OAuth callback
     */
    public function handleFacebookCallback(Request $request)
    {
        try {
            $state = json_decode($request->query('state'), true);
            $isRegistration = $state['register'] ?? false;

            $facebookUser = Socialite::driver('facebook')->stateless()->user();

            // Check if user exists
            $user = User::where('email', $facebookUser->getEmail())->first();

            if (!$user) {
                // Create new user
                $user = User::create([
                    'name' => $facebookUser->getName(),
                    'email' => $facebookUser->getEmail(),
                    'email_verified_at' => now(),
                    'password' => Hash::make(Str::random(32)), // Random password
                    'role' => 'customer', // Default role
                    'facebook_id' => $facebookUser->getId(),
                    'avatar' => $facebookUser->getAvatar(),
                ]);
            } else {
                // Update Facebook ID if not set
                if (!$user->facebook_id) {
                    $user->update(['facebook_id' => $facebookUser->getId()]);
                }
            }

            // Generate auth token
            $token = $user->createToken('auth_token')->plainTextToken;

            // Redirect to frontend with token
            $frontendUrl = env('FRONTEND_URL', 'http://localhost:5173');
            return redirect()->away(
                "{$frontendUrl}/auth/callback?token={$token}&role={$user->role}"
            );

        } catch (\Exception $e) {
            $frontendUrl = env('FRONTEND_URL', 'http://localhost:5173');
            return redirect()->away(
                "{$frontendUrl}/login?error=" . urlencode('Facebook authentication failed: ' . $e->getMessage())
            );
        }
    }
}

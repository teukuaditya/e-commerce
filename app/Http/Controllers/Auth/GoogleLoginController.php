<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class GoogleLoginController extends Controller
{
    /**
     * Redirect the user to the Google authentication page.
     */
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * Obtain the user information from Google.
     */
    public function handleGoogleCallback(Request $request)
    {
        try {
            $googleUser = Socialite::driver('google')->user();

            // Cek apakah user sudah ada berdasarkan google_id
            $user = User::where('google_id', $googleUser->getId())->first();

            if ($user) {
                // Update token jika user sudah ada
                $user->update([
                    'google_token' => $googleUser->token,
                    'google_refresh_token' => $googleUser->refreshToken,
                    'google_token_expires_at' => $googleUser->expiresIn 
                        ? now()->addSeconds($googleUser->expiresIn) 
                        : null,
                ]);
            } else {
                // Cek apakah email sudah terdaftar dengan akun biasa
                $existingUser = User::where('email', $googleUser->getEmail())->first();

                if ($existingUser) {
                    // Link akun Google ke akun yang sudah ada
                    $existingUser->update([
                        'google_id' => $googleUser->getId(),
                        'google_token' => $googleUser->token,
                        'google_refresh_token' => $googleUser->refreshToken,
                        'google_token_expires_at' => $googleUser->expiresIn 
                            ? now()->addSeconds($googleUser->expiresIn) 
                            : null,
                    ]);
                    $user = $existingUser;
                } else {
                    // Buat user baru
                    $user = User::create([
                        'name' => $googleUser->getName(),
                        'email' => $googleUser->getEmail(),
                        'password' => Hash::make(Str::random(24)), // Password random karena login via Google
                        'google_id' => $googleUser->getId(),
                        'google_token' => $googleUser->token,
                        'google_refresh_token' => $googleUser->refreshToken,
                        'google_token_expires_at' => $googleUser->expiresIn 
                            ? now()->addSeconds($googleUser->expiresIn) 
                            : null,
                        'email_verified_at' => now(), // Email dari Google sudah verified
                        'role' => 'user',
                    ]);
                }
            }

            // Login user
            Auth::login($user, true);

            // Redirect berdasarkan role
            if ($user->role === 'admin') {
                return redirect()->route('admin.dashboard.index');
            }

            return redirect()->route('user.home')->with('success', 'Login dengan Google berhasil!');
        } catch (\Exception $e) {
            \Log::error('Google login error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect()->route('login')->with('error', 'Gagal login dengan Google. Silakan coba lagi.');
        }
    }
}
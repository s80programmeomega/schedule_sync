<?php

namespace App\Http\Controllers\web\v1\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            $user = Auth::user();

            // Clear any existing 2FA session
            session()->forget('2fa_verified');

            if (!$user->hasVerifiedEmail()) {
                return redirect()->route('verification.notice');
            }

            // If user has 2FA enabled, redirect to verification
            if ($user->has2FA()) {
                return redirect()->route('2fa.verify');
            }

            return redirect()->intended(default: route('dashboard.index'));
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ]);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return view('auth.logout')->with('success', 'You have been logged out successfully.');
    }
}

<?php

namespace App\Http\Controllers\web\v1\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use PragmaRX\Google2FA\Google2FA;
use Exception;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;

class TwoFactorController extends Controller
{

public function show()
{
    try {
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login');
        }

        if (!$user->google2fa_secret) {
            $google2fa = new Google2FA();
            $secret = $google2fa->generateSecretKey();
            $user->update(['google2fa_secret' => $secret]);
            $user->refresh();
        }

        // Generate the TOTP URI
        $google2fa = new Google2FA();
        $qrCodeUri = $google2fa->getQRCodeUrl(
            config('app.name'),
            $user->email,
            $user->google2fa_secret
        );

        // Generate SVG QR code
        $renderer = new ImageRenderer(
            new RendererStyle(200),
            new SvgImageBackEnd()
        );
        $writer = new Writer($renderer);
        $qrCodeSvg = $writer->writeString($qrCodeUri);

        return view('auth.2fa.setup', compact('qrCodeSvg', 'user'));

    } catch (Exception $e) {
        Log::error('2FA Setup Error: ' . $e->getMessage());
        return redirect()->route('profile.show')->withErrors(['error' => 'Unable to generate 2FA setup']);
    }
}


    public function enable(Request $request)
    {
        $request->validate([
            'code' => 'required|string|size:6',
        ]);

        $user = Auth::user();

        // Verify the code
        $google2fa = new Google2FA();
        if (!$google2fa->verifyKey($user->google2fa_secret, $request->code)) {
            return back()->withErrors(['code' => 'Invalid verification code.']);
        }

        $user->update([
            'google2fa_enabled' => true,
            'google2fa_enabled_at' => now(),
        ]);

        return redirect()
            ->route('profile.show')
            ->with('success', '2FA has been enabled successfully!');
    }

    public function disable(Request $request)
    {
        $request->validate([
            'password' => 'required|current_password',
        ]);

        Auth::user()->disable2FA();

        return redirect()
            ->route('profile.show')
            ->with('success', '2FA has been disabled.');
    }

    public function verify()
    {
        return view('auth.2fa.verify');
    }

    public function validateCode(Request $request)
    {
        $request->validate([
            'code' => 'required|string|size:6',
        ]);

        $user = Auth::user();

        $google2fa = new Google2FA();
        if (!$google2fa->verifyKey($user->google2fa_secret, $request->code)) {
            return back()->withErrors(['code' => 'Invalid verification code.']);
        }

        session(['2fa_verified' => true]);

        return redirect()->intended(route('dashboard.index'));
    }
}

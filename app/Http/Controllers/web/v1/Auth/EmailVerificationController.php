<?php

namespace App\Http\Controllers\web\v1\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Email Verification Controller
 *
 * Handles email verification functionality including:
 * - Showing verification notice
 * - Processing verification links
 * - Resending verification emails
 */
class EmailVerificationController extends Controller
{
    /**
     * Show the email verification notice.
     *
     * This view is shown to users who need to verify their email.
     * It includes a button to resend the verification email.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function notice(Request $request)
    {
        // If user is already verified, redirect to dashboard
        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->intended('/dashboard');
        }

        return view('auth.verify-email');
    }

    /**
     * Mark the authenticated user's email address as verified.
     *
     * This method is called when user clicks the verification link.
     * Laravel automatically validates the signature and expiration.
     *
     * @param  \Illuminate\Foundation\Auth\EmailVerificationRequest  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function verify(EmailVerificationRequest $request)
    {
        // Check if email is already verified
        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->intended('/dashboard?verified=1');
        }

        // Mark email as verified
        if ($request->user()->markEmailAsVerified()) {
            // Fire the Verified event (useful for listeners)
            event(new Verified($request->user()));
        }

        return redirect()->intended('/dashboard?verified=1');
    }

    /**
     * Send a new email verification notification.
     *
     * This method handles resending verification emails.
     * It includes rate limiting to prevent spam.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function send(Request $request)
    {
        // Check if user is already verified
        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->intended('/dashboard');
        }

        // Send verification email
        $request->user()->sendEmailVerificationNotification();

        return back()->with('status', 'verification-link-sent');
    }
}

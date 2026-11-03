<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\View\View;


class EmailVerificationController extends Controller
{
   public function verify(Request $request, int $id, string $hash): View
    {
        $user = User::find($id);

        if (!$user) {
            return view('email-verified', [
                'success' => false,
                'title' => 'Invalid Verification Link',
                'message' => 'This verification link is not valid.',
                'showApprovalNote' => false,
            ]);
        }

        if (!hash_equals(sha1($user->getEmailForVerification()), $hash)) {
            return view('email-verified', [
                'success' => false,
                'title' => 'Invalid Verification Link',
                'message' => 'This verification link is not valid.',
                'showApprovalNote' => false,
            ]);
        }

        if (!URL::hasValidSignature($request)) {
            return view('email-verified', [
                'success' => false,
                'title' => 'Link Expired',
                'message' => 'This verification link has expired.',
                'showApprovalNote' => false,
            ]);
        }

        if ($user->hasVerifiedEmail()) {
            return view('email-verified', [
                'success' => true,
                'title' => 'Email Already Verified',
                'message' => 'Your email address was already verified.',
                'showApprovalNote' => $user->status !== 'approved',
            ]);
        }

        $user->markEmailAsVerified();

        return view('email-verified', [
            'success' => true,
            'title' => 'Email Verified Successfully',
            'message' => 'Thank you! Your email address has been verified.',
            'showApprovalNote' => true,
        ]);
    }

    /**
     * Public re-send of the verification email for users who cannot log in yet.
     *
     * Always responds with the same generic success message regardless of whether
     * the address exists or is already verified, so this endpoint cannot be used
     * to enumerate registered emails.
     */
    public function resend(Request $request): JsonResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        $email = Str::lower(trim($request->email));

        // Per-address limit on top of the per-IP route throttle.
        $key = 'resend-verification:' . sha1($email);
        if (RateLimiter::tooManyAttempts($key, 3)) {
            return response()->json([
                'success' => false,
                'code' => 'too_many_requests',
                'message' => 'Too many requests. Please wait a few minutes before trying again.'
            ], 429);
        }
        RateLimiter::hit($key, 600); // 3 per 10 minutes per address

        $user = User::where('email', $email)->first();

        if ($user && !$user->hasVerifiedEmail()) {
            $user->sendEmailVerificationNotification();
        }

        return response()->json([
            'success' => true,
            'message' => 'If an unverified account exists for that email, a new verification link has been sent.'
        ]);
    }
}

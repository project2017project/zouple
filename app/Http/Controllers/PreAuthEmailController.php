<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Mail\SendOtpMail; // Your custom Mailable for sending the OTP email

class PreAuthEmailController extends Controller
{
    /**
     * Send OTP to the provided email address.
     */
    public function sendOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email|unique:users,email',
        ]);

        $otp = rand(100000, 999999);
        $cacheKey = 'otp_email_' . $request->email;

        // Store OTP hash in Cache for 10 minutes
        Cache::put($cacheKey, Hash::make($otp), now()->addMinutes(10));

        // Send OTP via email
        Mail::to($request->email)->send(new SendOtpMail($otp));

        return response()->json(['message' => 'OTP sent to your email address.']);
    }

    /**
     * Verify the OTP and issue a proof token.
     */
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp'   => 'required|numeric',
        ]);

        $cacheKey = 'otp_email_' . $request->email;
        $hashedOtp = Cache::get($cacheKey);

        if (!$hashedOtp || !Hash::check($request->otp, $hashedOtp)) {
            return response()->json(['error' => 'Invalid or expired OTP.'], 422);
        }

        // Generate a temporary proof token valid for 30 minutes
        $token = Str::random(40);
        Cache::put('verified_email_token_' . $token, $request->email, now()->addMinutes(30));
        Cache::forget($cacheKey); // Clear the OTP after verification

        return response()->json([
            'message' => 'Email verified successfully!',
            'email_token' => $token,
        ]);
    }
}

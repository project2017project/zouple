<?php

namespace App\Http\Controllers\Auth;

use App\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Auth\Events\Verified;

class VerificationController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Email Verification Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles email verification for newly registered users.
    |
    | NOTE: Unlike Laravel's default implementation, this controller does NOT
    | require the 'auth' middleware. Since users are no longer automatically
    | logged in after registration, they must be able to verify their email
    | as a guest (e.g. clicking the link from a different browser/device).
    | The signed URL itself (containing the user id + a hash of the email)
    | is what proves the request is legitimate, not an active session.
    |
    */

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('signed')->only('verify');
        $this->middleware('throttle:6,1')->only('verify', 'resend');
    }

    /**
     * Mark the given user's email address as verified.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function verify(Request $request)
    {
        $user = User::findOrFail($request->route('id'));

        // The 'signed' middleware already guarantees the URL as a whole
        // hasn't been tampered with / hasn't expired. We additionally check
        // that the hash matches this user's email, exactly like Laravel's
        // default VerifiesEmails::verify() does with $request->user().
        if (! hash_equals(sha1($user->getEmailForVerification()), (string) $request->route('hash'))) {
            abort(403, 'Invalid verification link.');
        }

        if ($user->hasVerifiedEmail()) {
            $request->session()->flash('alert-success', 'Your email is already verified. You can log in.');
            return redirect('/');
        }

        $user->markEmailAsVerified();

        event(new Verified($user));

        $request->session()->flash('alert-success', 'Email verified successfully. You can now log in.');

        return redirect('/');
    }

    /**
     * Show a "please verify your email" notice.
     *
     * There's no dedicated notice page in this front-end (login/registration
     * happen in a modal), so we just send the user home with a message.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function show(Request $request)
    {
        $request->session()->flash('alert-danger', 'Your email address is not verified. Please verify your email before logging in.');

        return redirect('/');
    }

    /**
     * Resend the email verification notification, by email address, since
     * the user is not authenticated at this point.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function resend(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $user = User::where('email', $request->email)->first();

        if ($user && ! $user->hasVerifiedEmail()) {
            $user->sendEmailVerificationNotification();
        }

        // Deliberately vague so this endpoint can't be used to enumerate
        // which email addresses are registered.
        $request->session()->flash('alert-success', 'If that email is registered and unverified, a new verification link has been sent.');

        return redirect('/');
    }
}

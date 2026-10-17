<?php

namespace App\Http\Controllers\Client\Settings;

use App\Http\Controllers\Controller;
use App\Services\TwoFactorAuthenticator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;

class SecurityController extends Controller
{
    public function index(Request $request, TwoFactorAuthenticator $authenticator)
    {
        $user = Auth::guard('customer_user')->user();

        // A QR is rendered only while enrollment is in progress (a setup secret
        // is held in the session), never on an ordinary visit.
        $setupSecret = $request->session()->get('2fa_setup_secret');
        $qrSvg = $setupSecret
            ? $authenticator->qrCodeSvg($user->email, $setupSecret)
            : null;

        return view('client.settings.security', [
            'twoFactorEnabled' => $user->hasTwoFactorAppEnabled(),
            'setupSecret' => $setupSecret,
            'qrSvg' => $qrSvg,
        ]);
    }

    public function update(Request $request)
    {
        $request->validate([
            'current_password' => ['required'],
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        // Operate on the guard's own user instance so that the password hash the
        // AuthenticateSession middleware records for this session at end of
        // request matches what is persisted — otherwise the current device would
        // also be signed out on its next request.
        $guard = Auth::guard('customer_user');
        $user = $guard->user();

        if (! Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'The current password is incorrect.']);
        }

        $user->password = $request->password;
        // Rotate the remember_token to void every remember-me cookie, including
        // those on other devices.
        $user->setRememberToken(Str::random(60));
        $user->save();

        // Log out other active sessions via the password-hash check; this device
        // keeps its session on a freshly regenerated id.
        $guard->logoutOtherDevices($request->password);
        $request->session()->regenerate();

        return back()->with('success', 'Password updated successfully. You have been signed out of other devices.');
    }

    /**
     * Begin authenticator-app enrollment: generate a secret and stash it in the
     * session so the QR can be shown. It is persisted to the user only once a
     * valid code confirms the app is set up.
     */
    public function enableTwoFactor(Request $request, TwoFactorAuthenticator $authenticator)
    {
        $user = Auth::guard('customer_user')->user();

        if ($user->hasTwoFactorAppEnabled()) {
            return back()->with('error', 'Authenticator app is already enabled.');
        }

        $request->session()->put('2fa_setup_secret', $authenticator->generateSecret());

        return back()->with('status', 'Scan the QR code with your authenticator app, then enter the 6-digit code to finish.');
    }

    /**
     * Confirm enrollment by verifying a code against the pending setup secret.
     */
    public function confirmTwoFactor(Request $request, TwoFactorAuthenticator $authenticator)
    {
        $request->validate(['code' => ['required', 'digits:6']]);

        $secret = $request->session()->get('2fa_setup_secret');

        if (! $secret) {
            return back()->with('error', 'Start the setup again.');
        }

        if (! $authenticator->verify($secret, (string) $request->input('code'))) {
            return back()->withErrors(['code' => 'That code is not valid. Try again.']);
        }

        $user = Auth::guard('customer_user')->user();
        $user->two_factor_secret = $secret;
        $user->two_factor_confirmed_at = now();
        $user->save();

        $request->session()->forget('2fa_setup_secret');

        activity('auth')
            ->causedBy($user)
            ->event('2fa.app_enabled')
            ->log('Authenticator app enabled');

        return back()->with('success', 'Authenticator app enabled. You will use it at your next sign-in.');
    }

    /**
     * Disable the authenticator-app factor. Requires the current password so a
     * hijacked session cannot quietly strip a factor; login then falls back to
     * emailed codes.
     */
    public function disableTwoFactor(Request $request)
    {
        $request->validate(['current_password' => ['required']]);

        $user = Auth::guard('customer_user')->user();

        if (! Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'The current password is incorrect.']);
        }

        $user->two_factor_secret = null;
        $user->two_factor_confirmed_at = null;
        $user->save();

        $request->session()->forget('2fa_setup_secret');

        activity('auth')
            ->causedBy($user)
            ->event('2fa.app_disabled')
            ->log('Authenticator app disabled');

        return back()->with('success', 'Authenticator app disabled. Sign-in codes will be sent to your email.');
    }
}

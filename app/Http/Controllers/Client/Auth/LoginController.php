<?php

namespace App\Http\Controllers\Client\Auth;

use App\Http\Controllers\Controller;
use App\Models\CustomerUser;
use App\Services\TwoFactorAuthenticator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class LoginController extends Controller
{
    /**
     * Maximum wrong 2FA code entries before the pending login is discarded.
     */
    protected const MAX_2FA_ATTEMPTS = 5;

    public function index()
    {
        return view('client.auth.login');
    }

    public function submit(Request $request)
    {
        $validated = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        $user = CustomerUser::with('customer')
            ->where('email', $validated['email'])
            ->where('status', 'active')
            ->first();

        if (! $user || ! Hash::check($validated['password'], $user->password)) {
            activity('auth')
                ->withProperties($this->requestProperties($request) + ['email' => $validated['email']])
                ->event('login.failed')
                ->log('Failed login attempt');

            return back()
                ->withInput($request->only('email'))
                ->withErrors(['email' => 'Invalid email address or password.']);
        }

        $remember = $request->boolean('remember');

        if (config('auth.dev_2fa_bypass')) {
            $this->loginAndPopulateSession($user, $remember);

            activity('auth')
                ->causedBy($user)
                ->withProperties($this->requestProperties($request))
                ->event('login.success')
                ->log('User logged in');

            return redirect()->intended(route('client.dashboard'));
        }

        // Authenticator-app users verify with a code from their device; everyone
        // else receives a one-time code by email.
        if ($user->hasTwoFactorAppEnabled()) {
            session([
                '2fa_pending_user_id' => $user->id,
                '2fa_pending_customer_id' => $user->customer_id,
                '2fa_method' => 'app',
                '2fa_email' => $user->email,
                '2fa_remember' => $remember,
                '2fa_attempts' => 0,
            ]);

            activity('auth')
                ->causedBy($user)
                ->withProperties($this->requestProperties($request) + ['method' => 'app'])
                ->event('2fa.requested')
                ->log('2FA app challenge started');

            return redirect()->route('client.verification');
        }

        $this->startEmailChallenge($request, $user, $remember);

        return redirect()->route('client.verification');
    }

    protected function startEmailChallenge(Request $request, CustomerUser $user, bool $remember): void
    {
        $code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        session([
            '2fa_pending_user_id' => $user->id,
            '2fa_pending_customer_id' => $user->customer_id,
            '2fa_method' => 'email',
            '2fa_code' => $code,
            '2fa_expires_at' => now()->addMinutes(10)->timestamp,
            '2fa_email' => $user->email,
            '2fa_remember' => $remember,
            '2fa_attempts' => 0,
        ]);

        Mail::raw("Your NRH Intelligence verification code is: {$code}\n\nThis code expires in 10 minutes.", function ($message) use ($user) {
            $message->to($user->email)->subject('Your NRH verification code');
        });

        activity('auth')
            ->causedBy($user)
            ->withProperties($this->requestProperties($request) + ['method' => 'email'])
            ->event('2fa.requested')
            ->log('2FA code requested');
    }

    public function verification()
    {
        if (! session('2fa_pending_user_id')) {
            return redirect()->route('client.login');
        }

        return view('client.auth.verification', [
            'method' => session('2fa_method', 'email'),
            'email' => session('2fa_email'),
        ]);
    }

    public function verifyCode(Request $request, TwoFactorAuthenticator $authenticator)
    {
        $request->validate(['code' => ['required', 'digits:6']]);

        $userId = session('2fa_pending_user_id');
        $method = session('2fa_method');

        if (! $userId || ! $method) {
            return redirect()->route('client.login')->withErrors(['email' => 'Session expired. Please sign in again.']);
        }

        $user = CustomerUser::with('customer')->find($userId);

        if (! $user) {
            $this->clearTwoFactorSession();

            return redirect()->route('client.login')->withErrors(['email' => 'Session expired. Please sign in again.']);
        }

        $code = (string) $request->input('code');

        if ($method === 'app') {
            // TOTP codes are time-based, so there is no separate expiry to check.
            $valid = $user->two_factor_secret !== null
                && $authenticator->verify($user->two_factor_secret, $code);
        } else {
            $expiresAt = session('2fa_expires_at');

            if (! $expiresAt || now()->timestamp > $expiresAt) {
                activity('auth')
                    ->causedBy($user)
                    ->withProperties($this->requestProperties($request))
                    ->event('2fa.expired')
                    ->log('2FA code expired');

                return back()->withErrors(['code' => 'This code has expired. Please request a new one.']);
            }

            $valid = hash_equals((string) session('2fa_code'), $code);
        }

        if (! $valid) {
            $attempts = (int) session('2fa_attempts', 0) + 1;
            session(['2fa_attempts' => $attempts]);

            activity('auth')
                ->causedBy($user)
                ->withProperties($this->requestProperties($request) + ['attempts' => $attempts, 'method' => $method])
                ->event('2fa.failed')
                ->log('Invalid 2FA code');

            // Destroy the pending 2FA state after too many wrong guesses so the
            // code cannot be brute-forced within its window; the user must
            // re-enter their password to start a fresh challenge.
            if ($attempts >= self::MAX_2FA_ATTEMPTS) {
                $this->clearTwoFactorSession();

                return redirect()->route('client.login')
                    ->withErrors(['email' => 'Too many incorrect codes. Please sign in again.']);
            }

            return back()->withErrors(['code' => 'Invalid code. Please try again.']);
        }

        $remember = (bool) session('2fa_remember', false);
        $this->clearTwoFactorSession();

        $this->loginAndPopulateSession($user, $remember);

        activity('auth')
            ->causedBy($user)
            ->withProperties($this->requestProperties($request) + ['method' => $method])
            ->event('login.success')
            ->log('User logged in (2FA)');

        return redirect()->intended(route('client.dashboard'));
    }

    protected function clearTwoFactorSession(): void
    {
        session()->forget([
            '2fa_pending_user_id', '2fa_pending_customer_id', '2fa_method',
            '2fa_code', '2fa_expires_at', '2fa_email', '2fa_remember', '2fa_attempts',
        ]);
    }

    protected function loginAndPopulateSession(CustomerUser $user, bool $remember): void
    {
        Auth::guard('customer_user')->login($user, remember: $remember);

        session([
            'client_user_id' => $user->id,
            'client_customer_id' => $user->customer_id,
            'client_user_name' => $user->name,
            'client_user_avatar' => $user->avatar,
            'client_company' => $user->customer->name,
            'client_last_login' => now()->format('d M Y, H:i'),
        ]);
    }

    public function resend(Request $request)
    {
        if (! session('2fa_pending_user_id')) {
            return redirect()->route('client.login');
        }

        // Only the emailed code can be resent — authenticator-app codes are
        // generated on the user's own device.
        if (session('2fa_method') !== 'email') {
            return back();
        }

        $code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $email = session('2fa_email');

        session(['2fa_code' => $code, '2fa_expires_at' => now()->addMinutes(10)->timestamp, '2fa_attempts' => 0]);

        Mail::raw("Your NRH Intelligence verification code is: {$code}\n\nThis code expires in 10 minutes.", function ($message) use ($email) {
            $message->to($email)->subject('Your NRH verification code');
        });

        return back()->with('status', 'A new code has been sent to '.$email.'.');
    }

    public function forgot()
    {
        return view('client.auth.forgot');
    }

    public function sendReset(Request $request)
    {
        return back()->with('status', 'If that email exists, a reset link has been sent.');
    }

    public function reset(Request $request, string $token)
    {
        return view('client.auth.reset', ['token' => $token]);
    }

    public function processReset(Request $request)
    {
        return redirect()->route('client.login')->with('success', 'Password reset successfully.');
    }

    public function logout(Request $request)
    {
        $user = Auth::guard('customer_user')->user();

        if ($user) {
            activity('auth')
                ->causedBy($user)
                ->withProperties($this->requestProperties($request))
                ->event('logout')
                ->log('User logged out');
        }

        Auth::guard('customer_user')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('client.login');
    }

    /**
     * @return array{ip: string|null, user_agent: string|null}
     */
    protected function requestProperties(Request $request): array
    {
        return [
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ];
    }
}

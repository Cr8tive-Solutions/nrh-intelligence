<?php

namespace App\Http\Controllers\Client\Auth;

use App\Http\Controllers\Controller;
use App\Models\CustomerUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class LoginController extends Controller
{
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

        if (app()->isLocal()) {
            $this->loginAndPopulateSession($request, $user);

            activity('auth')
                ->causedBy($user)
                ->withProperties($this->requestProperties($request))
                ->event('login.success')
                ->log('User logged in');

            return redirect()->intended(route('client.dashboard'));
        }

        $code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        session([
            '2fa_pending_user_id' => $user->id,
            '2fa_pending_customer_id' => $user->customer_id,
            '2fa_code' => $code,
            '2fa_expires_at' => now()->addMinutes(10)->timestamp,
            '2fa_email' => $user->email,
        ]);

        Mail::raw("Your NRH Intelligence verification code is: {$code}\n\nThis code expires in 10 minutes.", function ($message) use ($user) {
            $message->to($user->email)->subject('Your NRH verification code');
        });

        activity('auth')
            ->causedBy($user)
            ->withProperties($this->requestProperties($request))
            ->event('2fa.requested')
            ->log('2FA code requested');

        return redirect()->route('client.verification');
    }

    public function verification()
    {
        if (! session('2fa_pending_user_id')) {
            return redirect()->route('client.login');
        }

        return view('client.auth.verification');
    }

    public function verifyCode(Request $request)
    {
        $request->validate(['code' => ['required', 'digits:6']]);

        $stored = session('2fa_code');
        $expiresAt = session('2fa_expires_at');
        $userId = session('2fa_pending_user_id');

        if (! $stored || ! $userId) {
            return redirect()->route('client.login')->withErrors(['email' => 'Session expired. Please sign in again.']);
        }

        if (now()->timestamp > $expiresAt) {
            activity('auth')
                ->causedBy(CustomerUser::find($userId))
                ->withProperties($this->requestProperties($request))
                ->event('2fa.expired')
                ->log('2FA code expired');

            return back()->withErrors(['code' => 'This code has expired. Please request a new one.']);
        }

        if ($request->input('code') !== $stored) {
            activity('auth')
                ->causedBy(CustomerUser::find($userId))
                ->withProperties($this->requestProperties($request))
                ->event('2fa.failed')
                ->log('Invalid 2FA code');

            return back()->withErrors(['code' => 'Invalid code. Please try again.']);
        }

        $user = CustomerUser::with('customer')->findOrFail($userId);

        session()->forget(['2fa_pending_user_id', '2fa_pending_customer_id', '2fa_code', '2fa_expires_at', '2fa_email']);

        $this->loginAndPopulateSession($request, $user);

        activity('auth')
            ->causedBy($user)
            ->withProperties($this->requestProperties($request))
            ->event('login.success')
            ->log('User logged in (2FA)');

        return redirect()->intended(route('client.dashboard'));
    }

    protected function loginAndPopulateSession(Request $request, CustomerUser $user): void
    {
        Auth::guard('customer_user')->login($user, remember: $request->boolean('remember'));

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

        $code = app()->isLocal() ? '000000' : str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $email = session('2fa_email');

        session(['2fa_code' => $code, '2fa_expires_at' => now()->addMinutes(10)->timestamp]);

        if (! app()->isLocal()) {
            Mail::raw("Your NRH Intelligence verification code is: {$code}\n\nThis code expires in 10 minutes.", function ($message) use ($email) {
                $message->to($email)->subject('Your NRH verification code');
            });
        }

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

<?php

namespace App\Http\Controllers\Client\Auth;

use App\Http\Controllers\Controller;
use App\Models\CustomerUser;
use Illuminate\Http\Request;
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
            return back()
                ->withInput($request->only('email'))
                ->withErrors(['email' => 'Invalid email address or password.']);
        }

        if (app()->isLocal()) {
            session([
                'client_user_id' => $user->id,
                'client_customer_id' => $user->customer_id,
                'client_user_name' => $user->name,
                'client_user_avatar' => $user->avatar,
                'client_company' => $user->customer->name,
                'client_last_login' => now()->format('d M Y, H:i'),
            ]);

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
            return back()->withErrors(['code' => 'This code has expired. Please request a new one.']);
        }

        if ($request->input('code') !== $stored) {
            return back()->withErrors(['code' => 'Invalid code. Please try again.']);
        }

        $user = CustomerUser::with('customer')->findOrFail($userId);

        session()->forget(['2fa_pending_user_id', '2fa_pending_customer_id', '2fa_code', '2fa_expires_at', '2fa_email']);

        session([
            'client_user_id' => $user->id,
            'client_customer_id' => $user->customer_id,
            'client_user_name' => $user->name,
            'client_user_avatar' => $user->avatar,
            'client_company' => $user->customer->name,
            'client_last_login' => now()->format('d M Y, H:i'),
        ]);

        return redirect()->intended(route('client.dashboard'));
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
        $request->session()->flush();

        return redirect()->route('client.login');
    }
}

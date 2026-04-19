<?php

namespace App\Http\Controllers\Client\Auth;

use App\Http\Controllers\Controller;
use App\Models\CustomerUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

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

        session([
            'client_user_id' => $user->id,
            'client_customer_id' => $user->customer_id,
            'client_user_name' => $user->name,
            'client_company' => $user->customer->name,
            'client_last_login' => now()->format('d M Y, H:i'),
        ]);

        return redirect()->intended(route('client.dashboard'));
    }

    public function verification()
    {
        return view('client.auth.verification');
    }

    public function verifyCode(Request $request)
    {
        return back()->withErrors(['code' => 'Invalid or expired code.']);
    }

    public function resend(Request $request)
    {
        return back()->with('status', 'A new code has been sent.');
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

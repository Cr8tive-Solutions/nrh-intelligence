<?php

namespace App\Http\Controllers\Client\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    public function index()
    {
        return view('client.auth.login');
    }

    public function submit(Request $request)
    {
        session([
            'client_user_id' => 1,
            'client_customer_id' => 1,
            'client_user_name' => 'Demo User',
            'client_company' => 'NRH Intelligence',
            'client_balance' => 1250.00,
        ]);

        return redirect()->route('client.dashboard');
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

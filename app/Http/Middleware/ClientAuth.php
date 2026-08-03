<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class ClientAuth
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! Auth::guard('customer_user')->check()) {
            return redirect()->route('client.login');
        }

        Auth::shouldUse('customer_user');

        // Keep the legacy client_customer_id / client_user_id session keys in
        // sync with the real auth guard on every request. Several controllers
        // still read tenant scoping from these keys (with unsafe fallbacks
        // being removed) — this makes the guard the single source of truth
        // regardless of how the session was established (password login,
        // remember-me, invitation accept, etc).
        $user = Auth::guard('customer_user')->user();
        $request->session()->put('client_customer_id', $user->customer_id);
        $request->session()->put('client_user_id', $user->id);

        return $next($request);
    }
}

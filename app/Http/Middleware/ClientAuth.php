<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class ClientAuth
{
    /**
     * Session key holding the Unix time the current session signed in.
     */
    public const LOGIN_AT_SESSION_KEY = 'client_login_at';

    public function handle(Request $request, Closure $next): Response
    {
        if (! Auth::guard('customer_user')->check()) {
            return redirect()->route('client.login');
        }

        // A user deactivated mid-session must lose access immediately, not
        // merely at the next idle timeout. Status is only enforced at password
        // login otherwise, so re-check the user's own status here on every
        // request and tear the session down if it is no longer active.
        // Remember-me cookies are killed separately by rotating the
        // remember_token when the account is deactivated.
        if (Auth::guard('customer_user')->user()->status !== 'active') {
            Auth::guard('customer_user')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('client.login')
                ->withErrors(['email' => 'Your account is no longer active. Please contact your administrator.']);
        }

        if ($this->sessionHasOutlivedAbsoluteLifetime($request)) {
            // Only the session is ended: the remember-me cookie (if any) is left
            // alone so it keeps working until its own expiry.
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('client.login')
                ->withErrors(['email' => 'Your session has expired. Please sign in again.']);
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

    /**
     * Whether this session signed in longer ago than the absolute lifetime,
     * however active it has been since. A session without a recorded sign-in
     * time (one restored from a remember-me cookie) starts its clock now.
     */
    protected function sessionHasOutlivedAbsoluteLifetime(Request $request): bool
    {
        $loginAt = $request->session()->get(self::LOGIN_AT_SESSION_KEY);

        if ($loginAt === null) {
            $request->session()->put(self::LOGIN_AT_SESSION_KEY, now()->timestamp);

            return false;
        }

        $lifetimeSeconds = (int) config('session.absolute_lifetime_minutes', 720) * 60;

        return now()->timestamp - (int) $loginAt > $lifetimeSeconds;
    }
}

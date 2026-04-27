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

        return $next($request);
    }
}

<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ClientAuth
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! session('client_user_id')) {
            return redirect()->route('client.login');
        }

        return $next($request);
    }
}

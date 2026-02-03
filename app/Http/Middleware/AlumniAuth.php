<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AlumniAuth
{
    /**
     * Handle an incoming request. Ensure the user is authenticated as alumni.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::guard('alumni')->check()) {
            return redirect()->route('alumni.login');
        }

        return $next($request);
    }
}

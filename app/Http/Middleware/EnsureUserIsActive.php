<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsActive
{
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check() && Auth::user()->status === 'pending') {
            if (! $request->routeIs(['pending-approval', 'verification.notice', 'verification.verify', 'logout'])) {
                return redirect()->route('pending-approval');
            }
        }

        return $next($request);
    }
}

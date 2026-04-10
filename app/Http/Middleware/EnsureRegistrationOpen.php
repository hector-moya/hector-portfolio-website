<?php

namespace App\Http\Middleware;

use App\Models\Invitation;
use App\Models\SiteSetting;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureRegistrationOpen
{
    public function handle(Request $request, Closure $next): Response
    {
        $mode = SiteSetting::get('registration_mode', 'closed');

        if ($mode === 'closed') {
            return to_route('login')
                ->with('status', 'Registration is currently closed.');
        }

        if ($mode === 'invitation') {
            $token = $request->query('token');

            if (! $token) {
                return to_route('login')
                    ->with('status', 'Registration is by invitation only.');
            }

            $invitation = Invitation::query()->where('token', $token)
                ->whereNull('accepted_at')
                ->where('expires_at', '>', now())
                ->first();

            if (! $invitation) {
                return to_route('login')
                    ->with('status', 'Invalid or expired invitation link.');
            }
        }

        return $next($request);
    }
}

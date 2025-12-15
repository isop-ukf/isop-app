<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ApiKeyAccessOnly
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $isSession = $request->hasCookie(config('session.cookie'));
        $isToken = $request->user()->currentAccessToken() !== null && !$isSession;

        if (!$isToken) {
            abort(403, 'Forbidden');
        }

        return $next($request);
    }
}

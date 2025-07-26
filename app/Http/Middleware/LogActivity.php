<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class LogActivity
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        $user = $request->user();

        if ($user) {
            $user->activityLogs()->create([
                'activity'   => 'Viewed Page',
                'url'        => $request->fullUrl(),
                'ip_address' => $request->ip(),
                'user_agent' => $request->header('user-agent'),
            ]);
        }

        return $response;
    }
}

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

        // Determine activity based on Livewire or HTTP request
        if ($request->header('X-Livewire') || $request->is('livewire/message/*')) {
            $activity = 'Livewire Action';
            $payload = $request->input('updates', []);
            if (isset($payload[0]['payload']['method'])) {
                $method = $payload[0]['payload']['method'];
                $component = $request->input('fingerprint.name', 'UnknownComponent');
                $activity = "Livewire: {$component}@{$method}";
            }
        } else {
            // For normal HTTP requests
            $activity = $request->method() . ' ' . $request->path();
        }

        foreach (array_keys(config('auth.guards')) as $guard) {
            $user = auth()->guard($guard)->user();
            if ($user) {
                if (method_exists($user, 'activityLogs')) {
                    $user->activityLogs()->create([
                        'activity'   => $activity,
                        'url'        => $request->fullUrl(),
                        'ip_address' => $request->ip(),
                        'user_agent' => $request->header('user-agent'),
                        'guard'      => $guard,
                    ]);
                }
                break;
            }
        }

        return $response;
    }
}

<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Log;

class CheckRole
{
    /**
     * Handle an incoming request.
     */
    public function handle($request, Closure $next, ...$roles)
    {
        // Ensure the user is authenticated
        if (!auth()->check()) {
            return redirect()->route('login.index');
        }

        $user = auth()->user();

        // Log debugging information
        Log::info('Authenticated User ID: ' . auth()->id());
        Log::info('Authenticated User Role: ' . $user->role);
        Log::info('Allowed Roles: ' . implode(', ', $roles));
        Log::info('Middleware Roles: ' . implode(', ', $roles));
        Log::info('Room ID: ' . $request->route('id'));

        // Check if the user's role is in the allowed roles
        if (!in_array($user->role, $roles)) {
            abort(403, 'Unauthorized');
        }

        return $next($request);
    }
}
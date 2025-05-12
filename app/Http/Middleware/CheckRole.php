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
    if (!auth()->check()) {
        return redirect()->route('login.index');
    }

    $userRole = strtolower(auth()->user()->role);
    $allowedRoles = array_map('strtolower', $roles);

    Log::info('Authenticated User ID: ' . auth()->id());
    Log::info('Authenticated User Role: ' . auth()->user()->role);
    Log::info('Allowed Roles: ' . implode(', ', $roles));
    Log::info('Middleware Roles: ' . implode(', ', $roles));

    if (!in_array($userRole, $allowedRoles)) {
        abort(403, 'Unauthorized');
    }

    return $next($request);
}

}
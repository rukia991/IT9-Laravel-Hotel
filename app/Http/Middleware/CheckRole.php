<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @return mixed
     */
    public function handle($request, Closure $next, ...$roles)
    {
        if (!auth()->check()) {
            return redirect()->route('login.index');
        }
    
        if (!in_array(auth()->user()->role, $roles)) {
            abort(403); // Unauthorized
        }
    
        return $next($request);
    }
    
}
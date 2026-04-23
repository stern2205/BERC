<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, $role)
    {
        if (!Auth::check()) {
            abort(403, 'Unauthorized');
        }

        // Normalize roles: lowercase, remove hyphens, trim spaces
        $userRole = strtolower(trim(str_replace('-', '', Auth::user()->role)));
        $requiredRole = strtolower(trim(str_replace('-', '', $role)));

        if ($userRole !== $requiredRole) {
            abort(403, 'Unauthorized');
        }

        return $next($request);
    }
}

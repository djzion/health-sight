<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckQualityImprovementAccess
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();
        $userRole = $user->role->name ?? null;

        // Allow access for Quality Improvement Personnel and Directors
        if (in_array($userRole, ['Quality Improvement Personnel', 'Director'])) {
            return $next($request);
        }

        // Redirect unauthorized users
        return redirect()->route('dashboard')->with('error', 'You do not have permission to access this section.');
    }
}

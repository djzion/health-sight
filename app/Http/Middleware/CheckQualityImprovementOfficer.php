<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class CheckQualityImprovementOfficer
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            // Log for debugging
            Log::info('User not authenticated, redirecting to login');
            return redirect()->route('login');
        }

        $user = Auth::user();

        // Log user role info
        Log::info('User role check: ' . ($user->role ? $user->role->name : 'No role'));

        // Check if user has the correct role
        if (!$user->role || $user->role->name !== 'Quality Improvement Personnel') {
            // Log the redirect
            Log::info('User does not have quality_improvement_officer role, redirecting to dashboard');
            return redirect()->route('dashboard')->with('error', 'You do not have permission to access the Quality Improvement Section.');
        }

        Log::info('User passed role check, proceeding to requested page');
        return $next($request);
    }
}

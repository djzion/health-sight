<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckUserApproval
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();

        if ($user->status === 'pending') {
            Auth::logout();
            return redirect()->route('login')->withErrors(['Your assessment portal is pending approval.']);
        }

        if ($user->status === 'rejected') {
            Auth::logout();
            return redirect()->route('login')->withErrors(['Your request for access to assessment portal was been rejected.']);
        }

        return $next($request);
    }

//     public function handle($request, Closure $next)
// {
//     if (!auth()->user()->is_approved) {
//         return redirect()->route('home')->with('error', 'Your account is pending approval.');
//     }
//     return $next($request);
// }

}


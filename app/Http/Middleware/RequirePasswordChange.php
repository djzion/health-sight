<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RequirePasswordChange
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check() && Auth::user()->password_change_required && !$request->is('change-password*')) {
            return redirect()->route('change-password.show')
                ->with('warning', 'You must change your password before continuing.');
        }

        return $next($request);
    }
}

<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    // public function store(Request $request): RedirectResponse
    // {
    //     // Validate the identity (email, username, or phone)
    //     $request->validate([
    //         'identity' => 'required|string',
    //         'password' => 'required|string',
    //     ]);

    //     // Determine if the identity is email, phone, or username
    //     $identity = $request->input('identity');

    //     $user = User::where('email', $identity)
    //         ->orWhere('phone', $identity)
    //         ->orWhere('username', $identity)
    //         ->first();

    //     if ($user && Hash::check($request->password, $user->password)) {
    //         // Authentication successful
    //         Auth::login($user);
    //         $request->session()->regenerate();

    //         return redirect()->intended(route('dashboard', absolute: false));
    //     }

    //     // If authentication fails
    //     throw ValidationException::withMessages([
    //         'identity' => ['The provided credentials are incorrect.'],
    //     ]);
    // }

    public function store(Request $request): RedirectResponse
{
    $request->validate([
        'identity' => 'required|string',
        'password' => 'required|string',
    ]);

    $field = filter_var($request->identity, FILTER_VALIDATE_EMAIL)
        ? 'email'
        : (is_numeric($request->identity) ? 'phone' : 'username');

    $user = User::where($field, $request->identity)->first();

    if (!$user) {
        return back()->withErrors(['identity' => 'These credentials do not match our records.']);
    }

    if ($user->status === 'Pending') {
        return back()->withErrors(['identity' => 'Your account is pending admin approval.']);
    }

    if ($user->status === 'Rejected') {
        return back()->withErrors(['identity' => 'Your account registration has been rejected.']);
    }

    if (Auth::attempt([$field => $request->identity, 'password' => $request->password])) {
        $request->session()->regenerate();

        return redirect()->route('dashboard');
    }

    return back()->withErrors(['password' => 'The provided password is incorrect.']);
}




    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}

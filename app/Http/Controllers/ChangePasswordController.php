<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class ChangePasswordController extends Controller
{
    public function show()
    {
        return view('auth.change-password');
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        $user = auth()->user();

        $user->update([
            'password' => Hash::make($validated['password']),
            'password_change_required' => false,
        ]);

        // Redirect based on user's role
        if ($user->role && $user->role->name === 'Admin') {
            return redirect()->route('admin.dashboard')
                ->with('success', 'Password changed successfully.');
        }

        return redirect()->route('dashboard')
            ->with('success', 'Password changed successfully.');
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Mail\UserApprovalNotification;
use App\Mail\UserRejectionNotification;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;


class AdminController extends Controller
{

    public function index()
    {
        $users = User::all();
        return view('admin.users', compact('users'));
    }

    public function pendingUsers()
    {
        $pendingUsers = User::where('status', 'pending')
            ->with(['lga', 'phc'])
            ->get();
        return view('admin.pending-users', compact('pendingUsers'));
    }

    public function approve(User $user)
    {
        $user->update(['status' => 'approved']);

        // Send approval email notification
        try {
            Mail::to($user->email)->send(new UserApprovalNotification($user));
            return redirect()->back()->with('success', 'User approved successfully and notification email sent.');
        } catch (\Exception $e) {
            Log::error('Failed to send approval email: ' . $e->getMessage());
            return redirect()->back()->with('success', 'User approved successfully, but email notification failed.');
        }
    }

    public function reject(User $user)
    {
        $user->update(['status' => 'rejected']);

        // Send rejection email notification
        try {
            Mail::to($user->email)->send(new UserRejectionNotification($user));
            return redirect()->back()->with('success', 'User rejected successfully and notification email sent.');
        } catch (\Exception $e) {
            Log::error('Failed to send rejection email: ' . $e->getMessage());
            return redirect()->back()->with('success', 'User rejected successfully, but email notification failed.');
        }
    }
    public function create()
    {
        $roles = \App\Models\Role::all();
        return view('admin.users.create', compact('roles'));
    }
    public function store(Request $request)
    {
        $request->validate([
            'full_name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users',
            'email' => 'required|string|email|max:255|unique:users',
            'phone' => 'required|string|max:20',
            'role_id' => 'required|exists:roles,id',
            'password' => 'required|string|min:6', // Make password required with minimum length
        ]);

        User::create([
            'full_name' => $request->full_name,
            'username' => $request->username,
            'email' => $request->email,
            'phone' => $request->phone,
            'role_id' => $request->role_id,
            'password' => Hash::make($request->password),
            'district_id' => 1,
            'phc_id' => 1,
            'lga_id' => 1,
            'status' => 'approved',
            'password_change_required' => true,
        ]);

        return redirect()->route('users.index')
            ->with('success', 'User created successfully!');
    }
    public function resetPassword(User $user)
    {
        $newPassword = 'Pwd123';

        $user->update([
            'password' => bcrypt($newPassword),
            'password_change_required' => true,
        ]);

        try {
            // Mail::to($user->email)->send(new PasswordResetEmail($user, $newPassword));
            // Uncomment the above line after creating the email class

            return redirect()->route('admin.users')->with(
                'success',
                'Password reset successfully. New password: ' . $newPassword
            );
        } catch (\Exception $e) {
            Log::error('Failed to send password reset email: ' . $e->getMessage());
            return redirect()->route('admin.users')->with(
                'success',
                'Password reset but email notification failed. New password: ' . $newPassword
            );
        }
    }

    // public function index()
    // {
    //     $users = User::all();
    //     return view('admin.users', compact('users'));
    // }


    // public function pendingUsers()
    // {
    //     $pendingUsers = User::where('status', 'pending')
    //         ->with(['lga', 'phc'])
    //         ->get();
    //     return view('admin.pending-users', compact('pendingUsers'));
    // }

    // public function approve(User $user)
    // {
    //     $user->update(['status' => 'approved']);
    //     return redirect()->back()->with('success', 'User approved successfully.');
    // }

    // public function reject(User $user)
    // {
    //     $user->update(['status' => 'rejected']);
    //     return redirect()->back()->with('success', 'User rejected successfully.');
    // }



}

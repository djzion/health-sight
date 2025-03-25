<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\District;
use App\Models\Lga;
use App\Models\Phc;
use App\Models\Role;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;
use App\Providers\RouteServiceProvider;
use Illuminate\Support\Facades\Log;
use App\Mail\UserRegistrationPending;
use App\Mail\AdminRegistrationNotification;
use App\Mail\UserApprovalNotification;
use Illuminate\Support\Facades\Mail;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        $districts = District::all();
        $roles = Role::all();
        $lgas = Lga::all();
        $phcs = Phc::all();

        return view('auth.register', compact('districts', 'roles', 'lgas', 'phcs'));
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */


    // public function store(Request $request)
    // {
    //     $request->validate([
    //         'full_name' => ['required', 'string', 'max:255'],
    //         'username' => ['required', 'string', 'max:255'],
    //         'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
    //         'phone' => ['required', 'string', 'unique:users'],
    //         'district_id' => ['required', 'exists:districts,id'],
    //         'lga_id' => ['required', 'exists:lgas,id'],
    //         'phc_id' => ['required', 'exists:phcs,id'],
    //         'role_id' => ['required', 'exists:roles,id'],
    //         'password' => ['required', 'string', 'min:8', 'confirmed'],
    //     ]);

    //     $user = User::create([
    //         'full_name' => $request->full_name,
    //         'username' => $request->username,
    //         'email' => $request->email,
    //         'phone' => $request->phone,
    //         'district_id' => $request->district_id,
    //         'lga_id' => $request->lga_id,
    //         'phc_id' => $request->phc_id,
    //         'role_id' => $request->role_id,
    //         'password' => Hash::make($request->password),
    //         'status' => 'pending',
    //     ]);

    //     event(new Registered($user));

    //     // Flash a success message to the session
    //     session()->flash('status', 'Your registration was successful and is pending admin approval.');

    //     // Redirect to the login page
    //     return redirect()->route('login');
    // }

    public function store(Request $request)
    {
        $request->validate([
            'full_name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'phone' => ['required', 'string', 'unique:users'],
            'district_id' => ['required', 'exists:districts,id'],
            'lga_id' => ['required', 'exists:lgas,id'],
            'phc_id' => ['required', 'exists:phcs,id'],
            'role_id' => ['required', 'exists:roles,id'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = User::create([
            'full_name' => $request->full_name,
            'username' => $request->username,
            'email' => $request->email,
            'phone' => $request->phone,
            'district_id' => $request->district_id,
            'lga_id' => $request->lga_id,
            'phc_id' => $request->phc_id,
            'role_id' => $request->role_id,
            'password' => Hash::make($request->password),
            'status' => 'pending',
        ]);

        event(new Registered($user));

        // Fetch a fresh user instance
        $freshUser = User::find($user->id);

        // Send user registration pending email
        try {
            Mail::to($freshUser->email)->send(new UserRegistrationPending($freshUser));
            Log::info('User registration email sent successfully to: ' . $freshUser->email);
        } catch (\Exception $e) {
            Log::error('Failed to send user registration email: ' . $e->getMessage());
        }

        // Send admin notifications
        try {
            $admins = User::whereIn('role_id', [1, 2])->get();

            foreach ($admins as $admin) {
                Mail::to($admin->email)->send(new AdminRegistrationNotification($freshUser));
                Log::info('Admin notification sent to: ' . $admin->email);
            }
        } catch (\Exception $e) {
            Log::error('Failed to send admin notification email: ' . $e->getMessage());
        }

        return redirect()->route('login')->with('success', 'Your registration was successful and is pending admin approval. A confirmation email has been sent to your email address.');
    }


    public function getLgas($districtId)
    {
        $lgas = Lga::where('district_id', $districtId)->get(['id', 'name']);
        return response()->json($lgas);

        Log::info('LGA: ' . $lgas);
    }


    public function getPhcs($lgaId)
    {
        $phcs = Phc::where('lga_id', $lgaId)->get(['id', 'name']);
        return response()->json($phcs);
        Log::info('PHC: ' . $phcs);
    }



    // public function approveUser($userId)
    // {
    //     $user = User::findOrFail($userId);

    //     if ($user->status === 'pending') {
    //         $user->status = 'approved';
    //         $user->save();

    //         return redirect()->route('admin.pending-users')->with('success', 'User approved successfully.');
    //     }

    //     return redirect()->route('admin.pending-users')->with('error', 'User is already approved or rejected.');
    // }

    public function approveUser($userId)
    {
        $user = User::findOrFail($userId);

        if ($user->status === 'pending') {
            $user->status = 'approved';
            $user->save();

            // Send approval notification to user
            try {
                Mail::to($user->email)->send(new UserApprovalNotification($user));
            } catch (\Exception $e) {
                Log::error('Failed to send approval notification: ' . $e->getMessage());
            }

            return redirect()->route('admin.pending-users')->with('success', 'User approved successfully and notification email sent.');
        }

        return redirect()->route('admin.pending-users')->with('error', 'User is already approved or rejected.');
    }



    public function rejectUser($userId)
    {
        $user = User::findOrFail($userId);

        if ($user->status === 'pending') {
            $user->status = 'rejected';
            $user->save();

            return response()->json(['message' => 'User rejected successfully.']);
        }

        return response()->json(['message' => 'User is already approved or rejected.'], 400);
    }

    public function listPendingUsers(): View
    {
        $pendingUsers = User::where('status', 'pending')->get();

        return view('admin.pending-users', compact('pendingUsers'));
    }
}

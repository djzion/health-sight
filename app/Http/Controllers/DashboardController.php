<?php

namespace App\Http\Controllers;

use App\Models\Assessment;
use App\Models\Phc;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        $totalUsers = User::count();
        $totalPHCs = Phc::count();
        $pendingApplications = User::where('status', 'pending')->count();
        $rejectedApplications = User::where('status', 'rejected')->count();
        $recentUsers = User::latest()->take(5)->get();
        $recentPHCs = Phc::latest()->take(5)->get();
        $newUserThreshold = now()->subDays(30);

        if ($user && $user->role && $user->role->name === 'Admin') {
            return view('admin.dashboard', compact(
                'totalUsers',
                'totalPHCs',
                'pendingApplications',
                'rejectedApplications',
                'recentUsers',
                'recentPHCs',
                'newUserThreshold'
            ));
        }

        return view('dashboard', compact(
            'totalUsers',
            'totalPHCs',
            'pendingApplications',
            'rejectedApplications',
            'recentUsers',
            'recentPHCs',
            'newUserThreshold'
        ));
    }
}

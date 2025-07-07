<?php

namespace App\Http\Controllers;

use App\Models\Assessment;
use App\Models\Phc;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\District; 
use App\Models\Lga;      

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        // Existing data
        $totalUsers = User::count();
        $totalPHCs = Phc::count();
        $pendingApplications = User::where('status', 'pending')->count();
        $rejectedApplications = User::where('status', 'rejected')->count();
        $recentUsers = User::latest()->take(5)->get();
        $recentPHCs = Phc::latest()->take(5)->get();
        $newUserThreshold = now()->subDays(30);

        // Add districts data for the QIP modal
        $districts = District::orderBy('name')->get();

        // Director-specific data (if needed)
        $totalPhcs = null;
        $completedAssessments = null;
        $pendingAssessments = null;
        $thisWeekCount = null;

        if ($user && $user->role && $user->role->name === 'director') {
            // Add director-specific calculations here if needed
            $totalPhcs = Phc::count(); // or filter by director's district
            $completedAssessments = 8; // Replace with actual calculation
            $pendingAssessments = 4;   // Replace with actual calculation
            $thisWeekCount = 3;        // Replace with actual calculation
        }

        if ($user && $user->role && $user->role->name === 'Admin') {
            return view('admin.dashboard', compact(
                'totalUsers',
                'totalPHCs',
                'pendingApplications',
                'rejectedApplications',
                'recentUsers',
                'recentPHCs',
                'newUserThreshold',
                'districts' // Add districts to admin dashboard too
            ));
        }

        return view('dashboard', compact(
            'totalUsers',
            'totalPHCs',
            'pendingApplications',
            'rejectedApplications',
            'recentUsers',
            'recentPHCs',
            'newUserThreshold',
            'districts',              // Add districts data
            'totalPhcs',              // Add director data
            'completedAssessments',
            'pendingAssessments',
            'thisWeekCount'
        ));
    }
}

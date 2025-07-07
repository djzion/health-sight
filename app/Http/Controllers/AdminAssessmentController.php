<?php

namespace App\Http\Controllers;

use App\Models\AssessmentPeriod;
use App\Models\AssessmentResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Carbon\Carbon;

class AdminAssessmentController extends Controller
{
    /**
     * Show the assessment settings form (your existing route)
     */
    public function showSettingsForm()
    {
        // Get next available assessment period
        $nextPeriod = AssessmentPeriod::getNextGeneralPeriod();
        $currentPeriod = AssessmentPeriod::getCurrentGeneralPeriod();

        $title = 'General Assessment Settings';
        $message = 'Configure general assessment periods and availability settings.';

        // If there's a current period
        if ($currentPeriod) {
            $message = "Current assessment period: {$currentPeriod->name} (ends {$currentPeriod->end_date->format('M j, Y')})";
        }

        // If there's a next period
        if ($nextPeriod) {
            $nextAvailableDate = $nextPeriod->start_date;
            $daysRemaining = now()->diffInDays($nextAvailableDate, false);
        } else {
            $nextAvailableDate = null;
            $daysRemaining = null;
        }

        return view('admin.assessments.settings', [
            'title' => $title,
            'message' => $message,
            'nextAvailableDate' => $nextAvailableDate,
            'daysRemaining' => $daysRemaining,
            'currentPeriod' => $currentPeriod,
            'nextPeriod' => $nextPeriod
        ]);
    }

    /**
     * Assessment periods management page (for the new system)
     */
    public function index()
    {
        $title = 'General Assessment Periods Management';
        $message = 'Manage general assessment periods, quarters, and scheduling.';

        return view('admin.assessment-periods.index', [
            'title' => $title,
            'message' => $message
        ]);
    }

    /**
     * Get all general assessment periods
     */
    public function getPeriods()
    {
        try {
            $periods = AssessmentPeriod::general()
                ->withCount('assessmentResponses')
                ->orderBy('year', 'desc')
                ->orderBy('quarter', 'desc')
                ->get();

            $formattedPeriods = $periods->map(function ($period) {
                return [
                    'id' => $period->id,
                    'name' => $period->name,
                    'quarter' => $period->quarter,
                    'year' => $period->year,
                    'start_date' => $period->start_date->format('Y-m-d'),
                    'end_date' => $period->end_date->format('Y-m-d'),
                    'description' => $period->description,
                    'is_active' => $period->is_active,
                    'status' => $period->status,
                    'days_remaining' => $period->days_remaining,
                    'assessments_count' => $period->assessment_responses_count ?? 0,
                    'created_at' => $period->created_at->format('M j, Y g:i A')
                ];
            });

            return response()->json([
                'success' => true,
                'periods' => $formattedPeriods
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching periods: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Create a new general assessment period
     */
    public function createPeriod(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'quarter' => ['required', Rule::in(['Q1', 'Q2', 'Q3', 'Q4'])],
                'year' => 'required|integer|min:2024|max:2030',
                'start_date' => 'required|date|after_or_equal:today',
                'end_date' => 'required|date|after:start_date',
                'description' => 'nullable|string|max:1000'
            ], [
                'quarter.in' => 'Quarter must be Q1, Q2, Q3, or Q4',
                'start_date.after_or_equal' => 'Start date must be today or in the future',
                'end_date.after' => 'End date must be after start date'
            ]);

            // Check for duplicate period
            $existingPeriod = AssessmentPeriod::where('quarter', $validated['quarter'])
                ->where('year', $validated['year'])
                ->where('assessment_type', 'general')
                ->first();

            if ($existingPeriod) {
                return response()->json([
                    'success' => false,
                    'message' => "General assessment period for {$validated['quarter']} {$validated['year']} already exists."
                ], 422);
            }

            // Create the period
            $period = AssessmentPeriod::create([
                'name' => $validated['name'],
                'quarter' => $validated['quarter'],
                'year' => $validated['year'],
                'start_date' => $validated['start_date'],
                'end_date' => $validated['end_date'],
                'description' => $validated['description'],
                'assessment_type' => 'general',
                'is_active' => true
            ]);

            return response()->json([
                'success' => true,
                'message' => 'General assessment period created successfully!',
                'period' => $period
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error creating period: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Toggle period status
     */
    public function togglePeriodStatus(AssessmentPeriod $period)
    {
        try {
            // Make sure this is a general assessment period
            if ($period->assessment_type !== 'general') {
                return response()->json([
                    'success' => false,
                    'message' => 'This is not a general assessment period.'
                ], 422);
            }

            $period->update([
                'is_active' => !$period->is_active
            ]);

            $status = $period->is_active ? 'activated' : 'deactivated';

            return response()->json([
                'success' => true,
                'message' => "General assessment period {$status} successfully!",
                'period' => $period
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating period status: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get general assessment statistics
     */
    public function getStatistics()
    {
        try {
            $totalPeriods = AssessmentPeriod::general()->count();
            $activePeriods = AssessmentPeriod::general()->active()->count();
            $currentPeriod = AssessmentPeriod::getCurrentGeneralPeriod();

            // Get total unique assessments (by PHC and period)
            $totalAssessments = AssessmentResponse::whereHas('assessmentPeriod', function ($query) {
                $query->where('assessment_type', 'general');
            })->select('phc_id', 'assessment_period_id')
              ->distinct()
              ->count();

            // Calculate completion rate (you can customize this logic)
            $completionRate = 0;
            if ($currentPeriod) {
                $totalPhcs = \App\Models\Phc::count();
                $completedPhcs = AssessmentResponse::where('assessment_period_id', $currentPeriod->id)
                    ->where('is_final_submission', true)
                    ->distinct('phc_id')
                    ->count();

                if ($totalPhcs > 0) {
                    $completionRate = round(($completedPhcs / $totalPhcs) * 100, 1);
                }
            }

            return response()->json([
                'success' => true,
                'statistics' => [
                    'total_periods' => $totalPeriods,
                    'active_periods' => $activePeriods,
                    'current_period' => $currentPeriod ? $currentPeriod->name : 'None',
                    'total_assessments' => $totalAssessments,
                    'avg_completion' => $completionRate
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching statistics: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete a general assessment period
     */
    public function deletePeriod(AssessmentPeriod $period)
    {
        try {
            // Make sure this is a general assessment period
            if ($period->assessment_type !== 'general') {
                return response()->json([
                    'success' => false,
                    'message' => 'This is not a general assessment period.'
                ], 422);
            }

            // Check if period has any assessment responses
            $hasResponses = $period->assessmentResponses()->exists();

            if ($hasResponses) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete period that has assessment responses. Deactivate it instead.'
                ], 422);
            }

            $period->delete();

            return response()->json([
                'success' => true,
                'message' => 'General assessment period deleted successfully!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting period: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Legacy method for setting next date (backwards compatibility)
     */
    public function setNextDate(Request $request)
    {
        try {
            $request->validate([
                'next_date' => 'required|date|after:today'
            ]);

            // For backwards compatibility, you could create a simple period
            // or update your legacy system here

            // Option 1: Create a simple period
            $date = Carbon::parse($request->next_date);
            $quarter = 'Q' . $date->quarter;
            $year = $date->year;

            $period = AssessmentPeriod::create([
                'name' => "Legacy Assessment {$quarter} {$year}",
                'quarter' => $quarter,
                'year' => $year,
                'start_date' => $date,
                'end_date' => $date->copy()->addDays(30), // 30-day window
                'description' => 'Created via legacy date setting',
                'assessment_type' => 'general',
                'is_active' => true
            ]);

            return redirect()
                ->route('admin.assessments.set-next-date-form')
                ->with('success', 'Assessment date set successfully! A new period has been created.');

        } catch (\Exception $e) {
            return redirect()
                ->route('admin.assessments.set-next-date-form')
                ->with('error', 'Error setting date: ' . $e->getMessage());
        }
    }
}

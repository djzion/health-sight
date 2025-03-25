<?php

namespace App\Http\Controllers;

use App\Models\Assessment;
use App\Models\AssessmentSection;
use App\Models\RoleCategory;
use Illuminate\Support\Facades\Auth;
use App\Models\AssessmentResponse;
use App\Models\District;
use App\Models\Lga;
use App\Models\Phc;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class _AssessmentController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $role = $user->role;

        \Log::info('User role check: ' . $role->name);

        $district = null;
        $lga = null;
        $phc = null;
        $showDirectorModal = false;
        $districts = collect();
        $localGovernments = collect();
        $phcs = collect();

        $locationSelected = session('assessment_location_selected', false);

        if ($role->name === 'director' || $role->name === 'Director') {
            $districts = District::orderBy('name')->get();
            $localGovernments = Lga::orderBy('name')->get();
            $phcs = Phc::orderBy('name')->get();

            if (!$locationSelected) {
                $assessments = collect();
                $existingResponses = collect()->keyBy('assessment_id');
                $sections = collect();
                $showDirectorModal = true;

                return view('assessments.index', compact(
                    'districts',
                    'localGovernments',
                    'phcs',
                    'assessments',
                    'existingResponses',
                    'sections',
                    'showDirectorModal'
                ));
            } else {
                $districtId = session('assessment_district_id');
                $lgaId = session('assessment_lga_id');
                $phcId = session('assessment_phc_id');

                $district = District::find($districtId)->name ?? 'Unknown';
                $lga = Lga::find($lgaId)->name ?? 'Unknown';
                $phc = Phc::find($phcId)->name ?? 'Unknown';
            }
        }

        $accessibleAssessmentIds = DB::table('assessment_role_category')
            ->join('role_categories', 'assessment_role_category.role_category_id', '=', 'role_categories.id')
            ->where('role_categories.role_id', $role->id)
            ->pluck('assessment_role_category.assessment_id')
            ->unique()
            ->toArray();

        $assessmentsWithSettings = Assessment::whereIn('id', $accessibleAssessmentIds)
            ->whereNull('parent_id')
            ->select('id', 'frequency', 'custom_interval_days', 'allow_resubmission', 'next_available_date')
            ->get()
            ->keyBy('id');

        $userResponses = AssessmentResponse::where('user_id', $user->id)
            ->whereIn('assessment_id', $accessibleAssessmentIds)
            ->select('assessment_id', 'created_at', 'updated_at')
            ->get();

        $now = now();
        $threeDaysAgo = $now->copy()->subDays(3);

        $assessmentIdsToRetake = [];
        $assessmentsWithRecentResponses = [];
        $responseMap = [];

        foreach ($userResponses as $response) {
            $responseDate = $response->updated_at ?? $response->created_at;
            $responseMap[$response->assessment_id] = $responseDate;

            $assessmentSettings = $assessmentsWithSettings[$response->assessment_id] ?? null;
            if (!$assessmentSettings) {
                continue;
            }

            $allowResubmission = $assessmentSettings->allow_resubmission ?? true;

            if (!$allowResubmission) {
                continue;
            }

            if ($assessmentSettings->next_available_date) {
                if ($now->gte($assessmentSettings->next_available_date)) {
                    $assessmentIdsToRetake[] = $response->assessment_id;
                }
                continue;
            }

            $frequency = $assessmentSettings->frequency ?? 'quarterly';
            $customIntervalDays = $assessmentSettings->custom_interval_days;
            $intervalDays = $this->getIntervalDaysFromFrequency($frequency, $customIntervalDays);
            $nextAvailableDate = $responseDate->copy()->addDays($intervalDays);

            if ($now->gte($nextAvailableDate)) {
                $assessmentIdsToRetake[] = $response->assessment_id;
            } elseif ($responseDate->gte($threeDaysAgo)) {
                $assessmentsWithRecentResponses[] = $response->assessment_id;
            }
        }

        $assessmentsWithoutResponses = [];
        foreach ($assessmentsWithSettings as $id => $settings) {
            if (isset($responseMap[$id])) {
                continue;
            }

            if ($settings->next_available_date) {
                if ($now->gte($settings->next_available_date)) {
                    $assessmentsWithoutResponses[] = $id;
                }
                continue;
            }

            $assessmentsWithoutResponses[] = $id;
        }

        $assessmentIdsToFetch = array_unique(
            array_merge(
                $assessmentIdsToRetake,
                $assessmentsWithRecentResponses,
                $assessmentsWithoutResponses
            )
        );

        if (empty($assessmentIdsToFetch)) {
            $earliestNextDate = null;
            $earliestAssessmentId = null;

            foreach ($assessmentsWithSettings as $id => $settings) {
                if (!$settings->allow_resubmission) {
                    continue;
                }

                if ($settings->next_available_date) {
                    if ($earliestNextDate === null || $settings->next_available_date->lt($earliestNextDate)) {
                        $earliestNextDate = $settings->next_available_date;
                        $earliestAssessmentId = $id;
                    }
                    continue;
                }

                if (isset($responseMap[$id])) {
                    $responseDate = $responseMap[$id];
                    $frequency = $settings->frequency ?? 'quarterly';
                    $customIntervalDays = $settings->custom_interval_days;
                    $intervalDays = $this->getIntervalDaysFromFrequency($frequency, $customIntervalDays);
                    $nextDate = $responseDate->copy()->addDays($intervalDays);

                    if ($earliestNextDate === null || $nextDate->lt($earliestNextDate)) {
                        $earliestNextDate = $nextDate;
                        $earliestAssessmentId = $id;
                    }
                }
            }

            return view('no-available-assessments', [
                'nextAvailableDate' => $earliestNextDate,
                'daysRemaining' => $earliestNextDate ? $now->diffInDays($earliestNextDate) : null
            ]);
        }

        $assessments = Assessment::whereIn('id', $assessmentIdsToFetch)
            ->whereNull('parent_id')
            ->with(['childQuestions', 'section'])
            ->orderBy('order')
            ->get();

        $assessments->each(function ($assessment) use (
            $assessmentIdsToRetake,
            $assessmentsWithoutResponses,
            $responseMap,
            $assessmentsWithSettings
        ) {
            $assessment->needs_retaking = in_array($assessment->id, $assessmentIdsToRetake);
            $assessment->is_new = in_array($assessment->id, $assessmentsWithoutResponses);

            if (isset($responseMap[$assessment->id])) {
                $assessment->last_response_date = $responseMap[$assessment->id];
            }

            $settings = $assessmentsWithSettings[$assessment->id] ?? null;
            if ($settings) {
                if ($settings->next_available_date) {
                    $assessment->next_available_date = $settings->next_available_date;
                    $assessment->admin_scheduled = true;
                } elseif (isset($responseMap[$assessment->id]) && $settings->allow_resubmission) {
                    $frequency = $settings->frequency ?? 'quarterly';
                    $customIntervalDays = $settings->custom_interval_days;
                    $intervalDays = $this->getIntervalDaysFromFrequency($frequency, $customIntervalDays);
                    $assessment->next_available_date = $responseMap[$assessment->id]->copy()
                        ->addDays($intervalDays);
                    $assessment->frequency = $frequency;
                }
            }
        });

        if ($role->name === 'director' && session('assessment_location_selected')) {
            $districtId = session('assessment_district_id');
            $lgaId = session('assessment_lga_id');
            $phcId = session('assessment_phc_id');

            $existingResponses = AssessmentResponse::where('user_id', $user->id)
                ->whereIn('assessment_id', $assessmentIdsToFetch)
                ->where('phc_id', $phcId)
                ->get()
                ->keyBy('assessment_id');
        } else {
            $existingResponses = AssessmentResponse::where('user_id', $user->id)
                ->whereIn('assessment_id', $assessmentIdsToFetch)
                ->get()
                ->keyBy('assessment_id');
        }

        $sections = AssessmentSection::whereHas('assessments', function ($query) use ($assessmentIdsToFetch) {
            $query->whereIn('id', $assessmentIdsToFetch);
        })->get();

        $assessments->each(function ($assessment) use ($existingResponses) {
            if (isset($existingResponses[$assessment->id])) {
                $response = $existingResponses[$assessment->id];
                $editCutoff = now()->subWeek();
                $assessment->can_edit = $response->created_at->gt($editCutoff);
                $assessment->edit_expires_at = $response->created_at->addWeek();

                $assessment->is_open_for_submission = $this->isAssessmentOpenForSubmission($assessment);
            } else {
                $assessment->is_open_for_submission = $this->isAssessmentOpenForSubmission($assessment);
            }
        });

        // Make sure to pass ALL necessary variables to the view
        return view('assessments.index', compact(
            'assessments',
            'sections',
            'existingResponses',
            'district',
            'lga',
            'phc',
            'showDirectorModal',
            'districts',
            'localGovernments',
            'phcs'
        ));
    }
    public function selectLocationForm()
    {
        $districts = District::orderBy('name')->get();
        $localGovernments = Lga::orderBy('name')->get();
        $phcs = Phc::orderBy('name')->get();

        return view('assessments.select-location', compact('districts', 'localGovernments', 'phcs'));
    }

    public function processLocationSelection(Request $request)
    {
        $validated = $request->validate([
            'district_id' => 'required|exists:districts,id',
            'lga_id' => 'required|exists:lgas,id',
            'phc_id' => 'required|exists:phcs,id',
        ]);

        session([
            'assessment_district_id' => $validated['district_id'],
            'assessment_lga_id' => $validated['lga_id'],
            'assessment_phc_id' => $validated['phc_id'],
            'assessment_location_selected' => true
        ]);

        return redirect()->route('assessments.index');
    }
    private function getIntervalDaysFromFrequency($frequency, $customIntervalDays = null)
    {
        switch ($frequency) {
            case 'quarterly':
                // 4 times per year = approximately 91 days
                return 91;
            case 'monthly':
                return 30;
            case 'bi-annually':
                return 182;
            case 'annually':
                return 365;
            case 'custom':
                return $customIntervalDays ?? 91; // Default to quarterly if custom days not set
            default:
                return 91; // Default to quarterly (4 times per year)
        }
    }
    public function store(Request $request)
    {
        $user = auth()->user();
        $responses = $request->input('responses', []);
        $successCount = 0;
        $errorCount = 0;

        // Get the PHC details from session if user is director
        $districtId = session('assessment_district_id');
        $lgaId = session('assessment_lga_id');
        $phcId = session('assessment_phc_id');
        $isDirector = $user->role->name === 'director';
        $freshAssessment = $request->has('fresh') || session('fresh_assessment');

        // If this is marked as a fresh assessment (just changed location),
        // store this in session so it persists across requests
        if ($freshAssessment) {
            session(['fresh_assessment' => true]);
        }

        Log::info('Store method called', [
            'is_director' => $isDirector,
            'district_id' => $districtId,
            'lga_id' => $lgaId,
            'phc_id' => $phcId,
            'fresh_assessment' => $freshAssessment,
            'session_data' => session()->all()
        ]);

        foreach ($responses as $assessmentId => $responseValue) {
            try {
                $assessment = Assessment::findOrFail($assessmentId);

                // Skip checking for existing response if this is a fresh assessment after location change
                if (!$freshAssessment) {
                    // Build a query to check if response exists
                    $query = AssessmentResponse::where('user_id', $user->id)
                        ->where('assessment_id', $assessmentId);

                    // For directors, add the PHC condition
                    if ($isDirector && $phcId) {
                        $query->where('phc_id', $phcId);
                    }

                    $existingResponse = $query->first();

                    if ($existingResponse) {
                        // Check if it's within the editable period (1 week)
                        $now = now();
                        $editCutoff = $now->copy()->subWeek();
                        $createdAt = $existingResponse->created_at;

                        if ($createdAt->lt($editCutoff)) {
                            // Outside editable period
                            return $this->handleOutsideEditableWindow($existingResponse, $assessment, $request);
                        }

                        // If within editable period, update instead of creating new
                        return $this->update($request);
                    }
                }

                // Create a complete data array including the response
                $responseData = [
                    'user_id' => $user->id,
                    'assessment_id' => $assessmentId,
                    'response' => is_array($responseValue) ? json_encode($responseValue) : $responseValue
                ];

                // Add PHC details if they exist in the session (for directors)
                if ($isDirector && $districtId && $lgaId && $phcId) {
                    $responseData['district_id'] = $districtId;
                    $responseData['lga_id'] = $lgaId;
                    $responseData['phc_id'] = $phcId;
                }

                Log::info('Creating new response', [
                    'assessment_id' => $assessmentId,
                    'user_id' => $user->id,
                    'is_director' => $isDirector,
                    'phc_id' => $phcId,
                    'data' => $responseData,
                    'fresh_assessment' => $freshAssessment
                ]);

                // Create new response
                AssessmentResponse::create($responseData);

                $successCount++;
            } catch (\Exception $e) {
                $errorCount++;
                Log::error('Failed to create response', [
                    'error' => $e->getMessage(),
                    'assessment_id' => $assessmentId,
                    'response_value' => $responseValue
                ]);
            }
        }

        // Clear the fresh_assessment flag after successful submission
        session()->forget('fresh_assessment');

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'You have successfully completed your assessment and response captured',
                'redirect' => route('dashboard')
            ]);
        }

        return redirect()->route('dashboard')
            ->with('success', 'You have successfully completed your assessment and response captured');
    }
    public function update(Request $request)
    {
        $user = auth()->user();
        $responses = $request->input('responses', []);
        $successCount = 0;
        $errorCount = 0;

        // Get the PHC details from session if user is director
        $districtId = session('assessment_district_id');
        $lgaId = session('assessment_lga_id');
        $phcId = session('assessment_phc_id');
        $isDirector = $user->role->name === 'director';

        foreach ($responses as $assessmentId => $responseValue) {
            try {
                $assessment = Assessment::findOrFail($assessmentId);

                // Build a query to find the existing response
                $query = AssessmentResponse::where('user_id', $user->id)
                    ->where('assessment_id', $assessmentId);

                // For directors, add the PHC condition
                if ($isDirector && $phcId) {
                    $query->where('phc_id', $phcId);
                }

                $existingResponse = $query->first();

                if (!$existingResponse) {
                    // No existing response - use store method instead
                    return $this->store($request);
                }

                // Check if it's within the editable period (1 week)
                $editCutoff = now()->subWeek();
                if ($existingResponse->created_at->lt($editCutoff)) {
                    // Outside editable period - handle with helper method
                    return $this->handleOutsideEditableWindow($existingResponse, $assessment, $request);
                }

                // Create update data array
                $responseData = [
                    'response' => is_array($responseValue) ? json_encode($responseValue) : $responseValue,
                    'updated_at' => now() // Explicitly update timestamp
                ];

                // Add PHC details if they exist in the session (for directors)
                if ($isDirector && $districtId && $lgaId && $phcId) {
                    $responseData['district_id'] = $districtId;
                    $responseData['lga_id'] = $lgaId;
                    $responseData['phc_id'] = $phcId;
                }

                Log::info('Updating existing response', [
                    'assessment_id' => $assessmentId,
                    'user_id' => $user->id,
                    'phc_id' => $phcId,
                    'response_id' => $existingResponse->id,
                    'data' => $responseData
                ]);

                // Update existing response
                $existingResponse->update($responseData);

                $successCount++;
            } catch (\Exception $e) {
                $errorCount++;
                Log::error('Failed to update response', [
                    'error' => $e->getMessage(),
                    'assessment_id' => $assessmentId,
                    'response_value' => $responseValue
                ]);
            }
        }

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Your assessment has been updated successfully',
                'redirect' => route('dashboard')
            ]);
        }

        return redirect()->route('dashboard')
            ->with('success', 'Your assessment has been updated successfully');
    }

    public function getChildQuestions(Assessment $assessment, Request $request)
    {
        $user = auth()->user();
        $selectedOption = $request->input('selected_option'); // 'yes' or 'no'
        $conditionPrefix = $selectedOption === 'no' ? 'if no' : 'if yes';

        $childQuestions = Assessment::where('parent_id', $assessment->id)
            ->whereRaw('LOWER(question) LIKE ?', [strtolower($conditionPrefix) . '%'])
            ->get();

        $existingResponses = AssessmentResponse::where('user_id', $user->id)
            ->whereIn('assessment_id', $childQuestions->pluck('id'))
            ->get()
            ->keyBy('assessment_id');

        $formattedQuestions = $childQuestions->map(function ($question) use ($existingResponses, $conditionPrefix) {
            $cleanQuestionText = preg_replace('/^' . preg_quote($conditionPrefix, '/') . '\s*/i', '', $question->question);

            return [
                'id' => $question->id,
                'question' => $question->question,
                'original_question' => $question->question,
                'response_type' => $question->response_type,
                'existing_response' => isset($existingResponses[$question->id]) ?
                    $existingResponses[$question->id]->response : null
            ];
        });

        return response()->json([
            'childQuestions' => $formattedQuestions
        ]);
    }
    private function handleOutsideEditableWindow($existingResponse, $assessment, $request)
    {
        // Check if admin has opened assessments for new submissions
        $isAssessmentOpen = $this->isAssessmentOpenForSubmission($assessment);

        if (!$isAssessmentOpen) {
            // Assessment is not currently open for submission
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'This assessment can no longer be edited and is not currently open for new submissions. Please check back later.',
                    'redirect' => route('dashboard')
                ]);
            }

            return redirect()->route('dashboard')
                ->with('info', 'This assessment can no longer be edited and is not currently open for new submissions. Please check back later.');
        }

        // For directors, we need to reset the location selection
        $user = auth()->user();
        if ($user->role->name === 'director') {
            session()->forget([
                'assessment_location_selected'
            ]);
        }

        // Assessment is open for new submission
        if ($request->ajax()) {
            return response()->json([
                'success' => false,
                'message' => 'This assessment can no longer be edited. You may submit a new assessment now.',
                'redirect' => route('assessments.index'),
                'create_new' => true
            ]);
        }

        return redirect()->route('assessments.index')
            ->with('info', 'This assessment can no longer be edited. Please submit a new assessment now.');
    }

    private function isAssessmentOpenForSubmission($assessment)
    {
        if ($assessment->next_available_date) {
            $now = now();

            return $now->gte($assessment->next_available_date);
        }

        return false;
    }
    public function setNextDate(Request $request)
    {
        $validated = $request->validate([
            'next_date' => 'required|date|after_or_equal:today',
        ]);

        // Update the next_available_date for all assessments
        Assessment::whereNull('parent_id')->update([
            'next_available_date' => $validated['next_date']
        ]);

        return redirect()->route('dashboard')
            ->with('success', 'Next assessment date set successfully for all assessments');
    }

    public function selectPHC(Request $request)
    {
        $validated = $request->validate([
            'district_id' => 'required|exists:districts,id',
            'lga_id' => 'required|exists:lgas,id',
            'phc_id' => 'required|exists:phcs,id',
        ]);

        $user = auth()->user();
        $newPhcId = $validated['phc_id'];

        // Check if director has previously assessed this PHC
        $latestAssessment = AssessmentResponse::where('user_id', $user->id)
            ->where('phc_id', $newPhcId)
            ->orderBy('created_at', 'desc')
            ->first();

        $editCutoff = now()->subWeek();
        $freshAssessment = true; // Default to true

        if ($latestAssessment) {
            // If there's a previous assessment for this PHC
            if ($latestAssessment->created_at->gt($editCutoff)) {
                // Within editable period - allow editing
                $freshAssessment = false;
                Log::info('Director selected previously assessed PHC within editable period', [
                    'phc_id' => $newPhcId,
                    'latest_assessment_date' => $latestAssessment->created_at
                ]);
            } else {
                // Outside editable period - treat as fresh assessment
                Log::info('Director selected previously assessed PHC outside editable period', [
                    'phc_id' => $newPhcId,
                    'latest_assessment_date' => $latestAssessment->created_at,
                    'edit_cutoff' => $editCutoff
                ]);
            }
        } else {
            // Never assessed this PHC before
            Log::info('Director selected a never-before-assessed PHC', [
                'phc_id' => $newPhcId
            ]);
        }

        session([
            'assessment_district_id' => $validated['district_id'],
            'assessment_lga_id' => $validated['lga_id'],
            'assessment_phc_id' => $validated['phc_id'],
            'assessment_location_selected' => true,
            'fresh_assessment' => $freshAssessment
        ]);

        Log::info('PHC Selection saved to session', [
            'district_id' => $validated['district_id'],
            'lga_id' => $validated['lga_id'],
            'phc_id' => $validated['phc_id'],
            'is_fresh_assessment' => $freshAssessment
        ]);

        return redirect()->route('assessments.index', $freshAssessment ? ['fresh' => true] : []);
    }

    public function showAssessment()
    {
        $user = auth()->user();

        // Check if location selection exists in session
        if ($user->role->name === 'director' && !session('assessment_location_selected')) {
            return redirect()->route('assessments.index');
        }

        // Get the location information if present
        $districtId = session('assessment_district_id');
        $lgaId = session('assessment_lga_id');
        $phcId = session('assessment_phc_id');

        // Get location names for display
        $district = null;
        $lga = null;
        $phc = null;

        if ($districtId && $lgaId && $phcId) {
            $district = District::find($districtId)->name ?? 'Unknown';
            $lga = Lga::find($lgaId)->name ?? 'Unknown';
            $phc = Phc::find($phcId)->name ?? 'Unknown';
        }

        // Now load assessments as in your normal flow
        // This is a simplified version of your normal assessment loading code
        $role = $user->role;
        $accessibleAssessmentIds = DB::table('assessment_role_category')
            ->join('role_categories', 'assessment_role_category.role_category_id', '=', 'role_categories.id')
            ->where('role_categories.role_id', $role->id)
            ->pluck('assessment_role_category.assessment_id')
            ->unique()
            ->toArray();

        // Fetch the assessments
        $assessments = Assessment::whereIn('id', $accessibleAssessmentIds)
            ->whereNull('parent_id')
            ->with(['childQuestions', 'section'])
            ->orderBy('order')
            ->get();

        // Fetch existing responses
        $existingResponses = AssessmentResponse::where('user_id', $user->id)
            ->whereIn('assessment_id', $assessments->pluck('id'))
            ->get()
            ->keyBy('assessment_id');

        // Group sections
        $sections = AssessmentSection::whereHas('assessments', function ($query) use ($assessments) {
            $query->whereIn('id', $assessments->pluck('id'));
        })->get();

        return view('assessments.index', compact('assessments', 'sections', 'existingResponses', 'district', 'lga', 'phc'));
    }

    public function resetLocation()
    {
        session()->forget([
            'assessment_district_id',
            'assessment_lga_id',
            'assessment_phc_id',
            'assessment_location_selected'
        ]);

        session()->forget('form_action');

        return redirect()->route('assessments.index', ['fresh' => true]);
    }

    public function resetLocation2()
    {
        session()->forget([
            'assessment_district_id',
            'assessment_lga_id',
            'assessment_phc_id',
            'assessment_location_selected'
        ]);

        session()->forget('form_action');

        return redirect()->route('assessments.index', ['fresh' => true]);
    }
}

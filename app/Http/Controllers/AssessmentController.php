<?php

namespace App\Http\Controllers;

use App\Models\Assessment;
use App\Models\AssessmentResponse;
use App\Models\AssessmentSection;
use App\Models\District;
use App\Models\Lga;
use App\Models\Phc;
use App\Models\TemporaryAssessment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AssessmentController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $role = $user->role;
        $now = now();
        $oneWeekAgo = $now->copy()->subWeek();

        \Log::info('User role check: ' . $role->name);

        $district = null;
        $lga = null;
        $phc = null;
        $showDirectorModal = false;
        $districts = collect();
        $localGovernments = collect();
        $phcs = collect();
        $canEdit = true;
        $editExpiresAt = null;
        $isNewAssessmentPeriod = false;

        $isDirector = $role->name === 'director' || $role->name === 'Director';

        if ($isDirector) {
            $districts = District::orderBy('name')->get();
            $localGovernments = Lga::orderBy('name')->get();
            $phcs = Phc::orderBy('name')->get();

            if (!session()->has('assessment_location_selected') || !session('assessment_location_selected')) {
                // Explicitly clear any location data that might exist in session
                session()->forget([
                    'assessment_district_id',
                    'assessment_lga_id',
                    'assessment_phc_id',
                    'assessment_location_selected'
                ]);

                // Show the modal and return empty assessments
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
                    'showDirectorModal',
                    'canEdit'
                ));
            } else {
                // Location is explicitly selected, get the location details
                $districtId = session('assessment_district_id');
                $lgaId = session('assessment_lga_id');
                $phcId = session('assessment_phc_id');

                if (!$districtId || !$lgaId || !$phcId) {
                    // If any location ID is missing, force selection again
                    session()->forget('assessment_location_selected');
                    return redirect()->route('assessments.index');
                }

                // Get location names for display
                $district = District::find($districtId)->name ?? 'Unknown';
                $lga = Lga::find($lgaId)->name ?? 'Unknown';
                $phc = Phc::find($phcId)->name ?? 'Unknown';
            }
        }

        // Get assessments based on role
        $accessibleAssessmentIds = DB::table('assessment_role_category')
            ->join('role_categories', 'assessment_role_category.role_category_id', '=', 'role_categories.id')
            ->where('role_categories.role_id', $role->id)
            ->pluck('assessment_role_category.assessment_id')
            ->unique()
            ->toArray();

        // Check if there's an active assessment period
        $newAssessmentPeriod = Assessment::whereIn('id', $accessibleAssessmentIds)
            ->whereNotNull('next_available_date')
            ->where('next_available_date', '<=', $now)
            ->exists();

        // STEP 1: Fetch the assessments FIRST (before trying to get responses)
        $assessments = Assessment::whereIn('id', $accessibleAssessmentIds)
            ->whereNull('parent_id')
            ->with(['childQuestions', 'section'])
            ->orderBy('order')
            ->get();

        // STEP 2: NOW get existing responses using the assessment IDs we just fetched
        $existingResponses = collect();

        if ($isDirector && session('assessment_location_selected')) {
            $phcId = session('assessment_phc_id');

            // Get responses for this PHC specifically - using the assessment IDs we now have
            $existingResponses = AssessmentResponse::where('user_id', $user->id)
                ->whereIn('assessment_id', $assessments->pluck('id'))
                ->where('phc_id', $phcId)
                ->get();

            \Log::info('Director existing responses', [
                'phc_id' => $phcId,
                'response_count' => count($existingResponses),
                'user_id' => $user->id
            ]);
        } else {
            $existingResponses = AssessmentResponse::where('user_id', $user->id)
                ->whereIn('assessment_id', $assessments->pluck('id'))
                ->get();
        }

        // STEP 3: Now check if responses can be edited (all within one week)
        $canEdit = true;
        $oldestResponse = null;

        if ($existingResponses->isNotEmpty()) {
            // Sort responses by creation date to find the oldest
            $oldestResponse = $existingResponses->sortBy('created_at')->first();
            $canEdit = $oldestResponse->created_at->gt($oneWeekAgo);
            $editExpiresAt = $oldestResponse->created_at->addWeek();

            \Log::info('Edit window check', [
                'oldest_response_date' => $oldestResponse->created_at,
                'one_week_ago' => $oneWeekAgo,
                'can_edit' => $canEdit,
                'expires_at' => $editExpiresAt
            ]);
        }

        // STEP 4: Create a proper response map keyed by assessment_id
        $existingResponsesMap = collect();

        foreach ($existingResponses as $response) {
            // Get the latest response for each assessment if multiple exist
            if (
                !$existingResponsesMap->has($response->assessment_id) ||
                $existingResponsesMap[$response->assessment_id]->created_at->lt($response->created_at)
            ) {
                $existingResponsesMap[$response->assessment_id] = $response;

                // Log to verify the map is being created correctly
                \Log::info('Added response to map', [
                    'assessment_id' => $response->assessment_id,
                    'response' => $response->response,
                    'created_at' => $response->created_at
                ]);
            }
        }

        // STEP 5: Handle business logic for edit window and new assessment periods
        if ($existingResponses->isNotEmpty() && !$canEdit) {
            // If responses exist but are too old to edit, check if there's a new assessment period
            if ($newAssessmentPeriod) {
                // New assessment period available - set flag to show empty form
                // while preserving old responses in the database
                $isNewAssessmentPeriod = true;

                \Log::info('New assessment period available', [
                    'is_director' => $isDirector,
                    'preserving_history' => true,
                    'showing_new_form' => true
                ]);
            } else {
                // No new assessment period and can't edit old responses - redirect to no-available
                $nextDate = Assessment::whereIn('id', $accessibleAssessmentIds)
                    ->whereNotNull('next_available_date')
                    ->orderBy('next_available_date')
                    ->value('next_available_date');

                // Clear the selected location for directors
                if ($isDirector) {
                    session()->forget([
                        'assessment_district_id',
                        'assessment_lga_id',
                        'assessment_phc_id',
                        'assessment_location_selected'
                    ]);
                }

                return redirect()->route('no-available-assessments')
                    ->with('info', 'Your previous assessment for this location can no longer be edited, and a new assessment period has not been opened yet.')
                    ->with('next_date', $nextDate);
            }
        }

        // STEP 6: If we're in a new assessment period, clear the response map
        // so no previous responses are shown (but they remain in the database)
        if ($isNewAssessmentPeriod) {
            $existingResponsesMap = collect();
            $existingResponses = collect(); // Also clear this to match
        }

        $existingResponses = collect();
        foreach ($existingResponsesMap as $assessmentId => $response) {
            $existingResponses[$assessmentId] = $response;
        }

        // STEP 7: Get sections for organization
        $sections = AssessmentSection::whereHas('assessments', function ($query) use ($assessments) {
            $query->whereIn('id', $assessments->pluck('id'));
        })->get();

        // STEP 8: Add response data to each assessment
        foreach ($assessments as $assessment) {
            if ($isNewAssessmentPeriod) {
                // For new assessment period, don't show previous responses
                $assessment->has_response = false;
                $assessment->can_edit = true;
                $assessment->is_new_period = true;
            } else {
                // Regular behavior - show existing responses if available
                $assessment->has_response = $existingResponsesMap->has($assessment->id);
                $assessment->can_edit = $canEdit;
                $assessment->edit_expires_at = $editExpiresAt;

                if ($existingResponsesMap->has($assessment->id)) {
                    $response = $existingResponsesMap[$assessment->id];
                    $assessment->user_response = $response->response;

                    \Log::info('Assessment has response', [
                        'assessment_id' => $assessment->id,
                        'response' => $response->response
                    ]);
                }
            }
        }

        // Add a notice for the new assessment period
        if ($isNewAssessmentPeriod) {
            // Show a message about new assessment period
            session()->flash('info', 'A new assessment period has been opened. Your previous responses are saved for reference, but you need to submit a new assessment.');
        }

        // Return the view with all necessary data
        return view('assessments.index', compact(
            'assessments',
            'sections',
            'existingResponsesMap',
            'existingResponses',
            'district',
            'lga',
            'phc',
            'showDirectorModal',
            'districts',
            'localGovernments',
            'phcs',
            'canEdit',
            'editExpiresAt',
            'isNewAssessmentPeriod'
        ));
    }

    public function store(Request $request)
    {
        $user = auth()->user();
        $responses = $request->input('responses', []);
        $isNewAssessmentPeriod = $request->input('is_new_period', false);
        $oneWeekAgo = now()->subWeek();

        // Check if user is director
        $isDirector = $user->role->name === 'director' || $user->role->name === 'Director';

        // For directors, verify location is selected
        if ($isDirector) {
            if (!session('assessment_location_selected')) {
                return redirect()->route('assessments.index')
                    ->with('error', 'Please select a location before submitting assessments.');
            }

            // Get location details from session
            $districtId = session('assessment_district_id');
            $lgaId = session('assessment_lga_id');
            $phcId = session('assessment_phc_id');

            if (!$districtId || !$lgaId || !$phcId) {
                return redirect()->route('assessments.index')
                    ->with('error', 'Location information is incomplete. Please select location again.');
            }
        }

        // Get all accessible assessment IDs for the user's role
        $role = $user->role;
        $accessibleAssessmentIds = DB::table('assessment_role_category')
            ->join('role_categories', 'assessment_role_category.role_category_id', '=', 'role_categories.id')
            ->where('role_categories.role_id', $role->id)
            ->pluck('assessment_role_category.assessment_id')
            ->unique()
            ->toArray();

        // Get all required assessments (excluding child questions)
        $requiredAssessments = Assessment::whereIn('id', $accessibleAssessmentIds)
            ->whereNull('parent_id')
            ->pluck('id')
            ->toArray();

        // Check if all required assessments have responses
        $missingResponses = [];
        foreach ($requiredAssessments as $assessmentId) {
            if (!isset($responses[$assessmentId]) || $responses[$assessmentId] === '') {
                $assessment = Assessment::find($assessmentId);
                if ($assessment) {
                    // Include the assessment ID to help identify duplicates
                    $missingResponses[] = "Question #{$assessmentId}: {$assessment->question}";
                } else {
                    $missingResponses[] = "Question #{$assessmentId}";
                }
            }
        }

        // If there are missing responses, return with error
        if (!empty($missingResponses)) {
            // Create a user-friendly message with question numbers to help locate them
            $errorMessage = 'All questions must be answered before submission. Missing answers for: ';

            $missingDetails = [];
            foreach (array_slice($missingResponses, 0, 3) as $missing) {
                // Extract the question ID using regex
                if (preg_match('/Question #(\d+)/', $missing, $matches)) {
                    $questionId = $matches[1];
                    $missingDetails[] = "Question #$questionId ($missing)";
                } else {
                    $missingDetails[] = $missing;
                }
            }

            $errorMessage .= implode(', ', $missingDetails);

            if (count($missingResponses) > 3) {
                $errorMessage .= ' and ' . (count($missingResponses) - 3) . ' more question(s)';
            }

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => $errorMessage
                ], 422);
            }

            return redirect()->back()
                ->with('error', $errorMessage)
                ->withInput();
        }

        // Log the start of response processing
        Log::info('Store method called', [
            'is_director' => $isDirector,
            'user_id' => $user->id,
            'response_count' => count($responses),
            'is_new_period' => $isNewAssessmentPeriod
        ]);

        $successCount = 0;
        $errorCount = 0;

        // Process each assessment response
        foreach ($responses as $assessmentId => $responseValue) {
            try {
                // Base response data
                $responseData = [
                    'user_id' => $user->id,
                    'assessment_id' => $assessmentId,
                    'response' => is_array($responseValue) ? json_encode($responseValue) : $responseValue
                ];

                // Add location information for directors
                if ($isDirector) {
                    $responseData['district_id'] = session('assessment_district_id');
                    $responseData['lga_id'] = session('assessment_lga_id');
                    $responseData['phc_id'] = session('assessment_phc_id');
                }

                // Check for existing response
                $query = AssessmentResponse::where('user_id', $user->id)
                    ->where('assessment_id', $assessmentId);

                // For directors, add PHC condition to check existing response
                if ($isDirector) {
                    $query->where('phc_id', session('assessment_phc_id'));
                }

                $existingResponse = $query->latest('created_at')->first();

                // Determine if we should update or create
                $shouldUpdate = false;

                if ($existingResponse) {
                    // Only update if within one week window and NOT a new assessment period
                    $shouldUpdate = $existingResponse->created_at->gt($oneWeekAgo) && !$isNewAssessmentPeriod;
                }

                if ($shouldUpdate) {
                    // Update the existing response
                    $existingResponse->update([
                        'response' => $responseData['response'],
                        'updated_at' => now()
                    ]);

                    Log::info('Updated existing response', [
                        'assessment_id' => $assessmentId,
                        'response_id' => $existingResponse->id,
                        'created_at' => $existingResponse->created_at
                    ]);
                } else {
                    // Create new response - preserving history
                    $newResponse = AssessmentResponse::create($responseData);

                    Log::info('Created new response', [
                        'assessment_id' => $assessmentId,
                        'response_id' => $newResponse->id,
                        'preserving_history' => true
                    ]);
                }

                $successCount++;
            } catch (\Exception $e) {
                $errorCount++;
                Log::error('Failed to save response', [
                    'error' => $e->getMessage(),
                    'assessment_id' => $assessmentId
                ]);
            }
        }

        // Check if all responses were processed successfully
        if ($errorCount > 0) {
            $message = "Some responses could not be saved. Please try again.";

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => $message
                ], 500);
            }

            return redirect()->back()->with('error', $message);
        }

        // For directors, clear the location selection after successful submission
        if ($isDirector) {
            session()->forget([
                'assessment_district_id',
                'assessment_lga_id',
                'assessment_phc_id',
                'assessment_location_selected'
            ]);
        }
        if (auth()->user()->role->name === 'director') {
            $phcId = session('assessment_phc_id');
            if ($phcId) {
                $this->clearTemporaryResponses($phcId);
            }
        }

        // Return success response
        $message = 'Assessment responses saved successfully';

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'redirect' => route('dashboard')
            ]);
        }

        return redirect()->route('dashboard')->with('success', $message);

    }

    public function getChildQuestions(Assessment $assessment, Request $request)
    {
        $user = auth()->user();
        $selectedOption = $request->input('selected_option'); // 'yes' or 'no'

        if ($selectedOption == 'yes') {
            $conditionPrefix = 'if yes';

        } else if ($selectedOption == 'no') {
            $conditionPrefix = 'if no';

        } else if ($selectedOption == 'n/a') {
            $conditionPrefix = 'if n/a';

        }

        // $conditionPrefix = $selectedOption === 'no' ? 'if no' : 'if yes';

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

    public function processLocationSelection(Request $request)
    {
        $validated = $request->validate([
            'district_id' => 'required|exists:districts,id',
            'lga_id' => 'required|exists:lgas,id',
            'phc_id' => 'required|exists:phcs,id',
        ]);

        $user = auth()->user();
        $isDirector = $user->role->name === 'director' || $user->role->name === 'Director';

        if (!$isDirector) {
            return redirect()->route('dashboard')
                ->with('error', 'Unauthorized access.');
        }

        session([
            'assessment_district_id' => $validated['district_id'],
            'assessment_lga_id' => $validated['lga_id'],
            'assessment_phc_id' => $validated['phc_id'],
            'assessment_location_selected' => true
        ]);

        Log::info('Director selected location', [
            'user_id' => $user->id,
            'district_id' => $validated['district_id'],
            'lga_id' => $validated['lga_id'],
            'phc_id' => $validated['phc_id']
        ]);

        return redirect()->route('assessments.index');
    }

    public function resetLocation()
    {
        session()->forget([
            'assessment_district_id',
            'assessment_lga_id',
            'assessment_phc_id',
            'assessment_location_selected'
        ]);

        return redirect()->route('assessments.index');
    }

    public function selectPHC(Request $request)
    {
        $validated = $request->validate([
            'district_id' => 'required|exists:districts,id',
            'lga_id' => 'required|exists:lgas,id',
            'phc_id' => 'required|exists:phcs,id',
        ]);

        $user = auth()->user();
        $isDirector = $user->role->name === 'director' || $user->role->name === 'Director';

        if (!$isDirector) {
            return redirect()->route('dashboard')
                ->with('error', 'Unauthorized access.');
        }

        session([
            'assessment_district_id' => $validated['district_id'],
            'assessment_lga_id' => $validated['lga_id'],
            'assessment_phc_id' => $validated['phc_id'],
            'assessment_location_selected' => true
        ]);

        Log::info('Director selected location via selectPHC', [
            'user_id' => $user->id,
            'district_id' => $validated['district_id'],
            'lga_id' => $validated['lga_id'],
            'phc_id' => $validated['phc_id']
        ]);

        return redirect()->route('assessments.index');
    }

    public function noAvailableAssessments()
    {
        $nextDate = session('next_date');
        $info = session('info');
        $nextDate = session('next_date');
        if (is_string($nextDate)) {
            $nextDate = \Carbon\Carbon::parse($nextDate);
        }

        return view('no-available-assessments', [
            'nextAvailableDate' => $nextDate,
            'daysRemaining' => $nextDate ? now()->diffInDays($nextDate) : null,
            'info' => $info
        ]);
    }
    private function isAssessmentOpenForSubmission($assessment)
    {
        if ($assessment->next_available_date) {
            $now = now();

            return $now->gte($assessment->next_available_date);
        }

        return false;
    }

    public function update(Request $request)
    {
        $user = auth()->user();
        $responses = $request->input('responses', []);

        // Check if user is director
        $isDirector = $user->role->name === 'director' || $user->role->name === 'Director';

        // For directors, verify location is selected
        if ($isDirector) {
            if (!session('assessment_location_selected')) {
                return redirect()->route('assessments.index')
                    ->with('error', 'Please select a location before updating assessments.');
            }

            $phcId = session('assessment_phc_id');
            if (!$phcId) {
                return redirect()->route('assessments.index')
                    ->with('error', 'Location information is incomplete. Please select location again.');
            }
        }

        // Define the one-week cutoff for edits
        $oneWeekAgo = now()->subWeek();

        // For each assessment response, verify it's within the editable window
        $canEdit = true;
        $nonEditableResponses = [];

        foreach ($responses as $assessmentId => $responseValue) {
            // Build query to find existing response
            $query = AssessmentResponse::where('user_id', $user->id)
                ->where('assessment_id', $assessmentId);

            // Add PHC filter for directors
            if ($isDirector) {
                $query->where('phc_id', session('assessment_phc_id'));
            }

            $existingResponse = $query->first();

            // If no existing response or response is too old to edit
            if (!$existingResponse) {
                $nonEditableResponses[] = "Question #$assessmentId (no previous response)";
                $canEdit = false;
                continue;
            }

            // Check if response is within the one-week edit window
            if ($existingResponse->created_at->lt($oneWeekAgo)) {
                $assessment = Assessment::find($assessmentId);
                $questionText = $assessment ? $assessment->question : "Question #$assessmentId";
                $nonEditableResponses[] = $questionText;
                $canEdit = false;
            }
        }

        // If any responses are outside the edit window, redirect to no-available-assessments
        if (!$canEdit) {
            Log::info('Update attempt outside of edit window', [
                'user_id' => $user->id,
                'is_director' => $isDirector,
                'non_editable' => $nonEditableResponses
            ]);

            return redirect()->route('no-available-assessments')
                ->with('info', 'Your assessment can no longer be edited as it has been more than a week since submission.');
        }

        // All responses are within the edit window, proceed with update
        $successCount = 0;
        $errorCount = 0;

        foreach ($responses as $assessmentId => $responseValue) {
            try {
                // Build query to find the response to update
                $query = AssessmentResponse::where('user_id', $user->id)
                    ->where('assessment_id', $assessmentId);

                // Add PHC filter for directors
                if ($isDirector) {
                    $query->where('phc_id', session('assessment_phc_id'));
                }

                $existingResponse = $query->first();

                if (!$existingResponse) {
                    // This shouldn't happen at this point, but just in case
                    continue;
                }

                // Update the response
                $existingResponse->update([
                    'response' => is_array($responseValue) ? json_encode($responseValue) : $responseValue,
                    'updated_at' => now()
                ]);

                Log::info('Updated assessment response', [
                    'assessment_id' => $assessmentId,
                    'response_id' => $existingResponse->id,
                    'is_director' => $isDirector
                ]);

                $successCount++;
            } catch (\Exception $e) {
                $errorCount++;
                Log::error('Failed to update response', [
                    'error' => $e->getMessage(),
                    'assessment_id' => $assessmentId
                ]);
            }
        }

        // For directors, clear the location selection after update
        if ($isDirector) {
            session()->forget([
                'assessment_district_id',
                'assessment_lga_id',
                'assessment_phc_id',
                'assessment_location_selected'
            ]);
        }

        // Return appropriate response
        $message = 'Assessment responses updated successfully';

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'redirect' => route('dashboard')
            ]);
        }

        return redirect()->route('dashboard')->with('success', $message);
    }

    public function setNextDate(Request $request)
    {
        $validated = $request->validate([
            'next_date' => 'required|date|after_or_equal:today',
        ]);

        Assessment::whereNull('parent_id')->update([
            'next_available_date' => $validated['next_date']
        ]);

        return redirect()->route('dashboard')
            ->with('success', 'Next assessment date set successfully for all assessments');
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

    public function saveTemporary(Request $request)
    {
        try {
            $user = auth()->user();
            $phcId = $request->input('phc_id');
            $responses = $request->input('responses', []);
            $currentPage = $request->input('current_page', 0);

            if (!$phcId) {
                return response()->json([
                    'success' => false,
                    'message' => 'PHC ID is required'
                ], 400);
            }

            // Check for existing temporary record for this user and PHC
            $tempAssessment = TemporaryAssessment::where('user_id', $user->id)
                ->where('phc_id', $phcId)
                ->first();

            if ($tempAssessment) {
                // Update existing record
                $tempAssessment->update([
                    'responses' => json_encode($responses),
                    'current_page' => $currentPage,
                    'updated_at' => now()
                ]);
            } else {
                // Create new record
                TemporaryAssessment::create([
                    'user_id' => $user->id,
                    'phc_id' => $phcId,
                    'responses' => json_encode($responses),
                    'current_page' => $currentPage,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Temporary responses saved successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('Error saving temporary responses: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to save temporary responses: ' . $e->getMessage()
            ], 500);
        }
    }


    public function loadTemporary($phcId)
    {
        try {
            $user = auth()->user();

            // Get the temporary assessment for this user and PHC
            $tempAssessment = TemporaryAssessment::where('user_id', $user->id)
                ->where('phc_id', $phcId)
                ->first();

            if ($tempAssessment) {
                return response()->json([
                    'success' => true,
                    'data' => [
                        'responses' => json_decode($tempAssessment->responses, true),
                        'current_page' => $tempAssessment->current_page,
                        'updated_at' => $tempAssessment->updated_at
                    ]
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'No temporary responses found'
            ]);
        } catch (\Exception $e) {
            Log::error('Error loading temporary responses: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to load temporary responses: ' . $e->getMessage()
            ], 500);
        }
    }


    private function clearTemporaryResponses($phcId)
    {
        try {
            $user = auth()->user();

            // Delete temporary assessment for this user and PHC
            TemporaryAssessment::where('user_id', $user->id)
                ->where('phc_id', $phcId)
                ->delete();

            return true;
        } catch (\Exception $e) {
            Log::error('Error clearing temporary responses: ' . $e->getMessage());
            return false;
        }
    }
}

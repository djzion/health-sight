<?php

namespace App\Http\Controllers;

use App\Models\Assessment;
use App\Models\AssessmentPeriod;
use App\Models\AssessmentResponse;
use App\Models\AssessmentSection;
use App\Models\District;
use App\Models\Lga;
use App\Models\Phc;
use App\Models\TemporaryAssessment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;


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
                session()->forget([
                    'assessment_district_id',
                    'assessment_lga_id',
                    'assessment_phc_id',
                    'assessment_location_selected'
                ]);

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
                $districtId = session('assessment_district_id');
                $lgaId = session('assessment_lga_id');
                $phcId = session('assessment_phc_id');

                if (!$districtId || !$lgaId || !$phcId) {
                    session()->forget('assessment_location_selected');
                    return redirect()->route('assessments.index');
                }

                $district = District::find($districtId)->name ?? 'Unknown';
                $lga = Lga::find($lgaId)->name ?? 'Unknown';
                $phc = Phc::find($phcId)->name ?? 'Unknown';
            }
        }

        // Get accessible assessment IDs
        $accessibleAssessmentIds = DB::table('assessment_role_category')
            ->join('role_categories', 'assessment_role_category.role_category_id', '=', 'role_categories.id')
            ->where('role_categories.role_id', $role->id)
            ->pluck('assessment_role_category.assessment_id')
            ->unique()
            ->toArray();

        // 🔥 CHECK FOR ACTIVE GENERAL ASSESSMENT PERIOD (FIXED)
        $currentGeneralPeriod = AssessmentPeriod::getCurrentGeneralPeriod();
        $hasActiveAssessmentPeriod = $currentGeneralPeriod !== null;

        \Log::info('Assessment period check', [
            'current_period' => $currentGeneralPeriod ? $currentGeneralPeriod->name : 'None',
            'has_active_period' => $hasActiveAssessmentPeriod
        ]);

        // Get assessments
        $assessments = Assessment::whereIn('id', $accessibleAssessmentIds)
            ->whereNull('parent_id')
            ->with(['childQuestions', 'section'])
            ->orderBy('order')
            ->get();

        $existingResponses = collect();

        if ($isDirector && session('assessment_location_selected')) {
            $phcId = session('assessment_phc_id');

            // Check for existing responses in current period
            if ($currentGeneralPeriod) {
                $existingResponses = AssessmentResponse::where('user_id', $user->id)
                    ->whereIn('assessment_id', $assessments->pluck('id'))
                    ->where('phc_id', $phcId)
                    ->where('assessment_period_id', $currentGeneralPeriod->id)
                    ->get();
            }

            \Log::info('Director existing responses', [
                'phc_id' => $phcId,
                'response_count' => count($existingResponses),
                'user_id' => $user->id,
                'period_id' => $currentGeneralPeriod ? $currentGeneralPeriod->id : 'None'
            ]);
        } else {
            // For non-directors, check current period
            if ($currentGeneralPeriod) {
                $existingResponses = AssessmentResponse::where('user_id', $user->id)
                    ->whereIn('assessment_id', $assessments->pluck('id'))
                    ->where('assessment_period_id', $currentGeneralPeriod->id)
                    ->get();
            }
        }

        $canEdit = true;
        $oldestResponse = null;

        if ($existingResponses->isNotEmpty()) {
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

        $existingResponsesMap = collect();

        foreach ($existingResponses as $response) {
            if (
                !$existingResponsesMap->has($response->assessment_id) ||
                $existingResponsesMap[$response->assessment_id]->created_at->lt($response->created_at)
            ) {
                $existingResponsesMap[$response->assessment_id] = $response;

                \Log::info('Added response to map', [
                    'assessment_id' => $response->assessment_id,
                    'response' => $response->response,
                    'created_at' => $response->created_at
                ]);
            }
        }

        // 🔥 UPDATED LOGIC FOR ASSESSMENT AVAILABILITY
        if ($existingResponses->isNotEmpty() && !$canEdit) {
            if ($hasActiveAssessmentPeriod) {
                $isNewAssessmentPeriod = true;

                \Log::info('New assessment period available', [
                    'is_director' => $isDirector,
                    'preserving_history' => true,
                    'showing_new_form' => true,
                    'current_period' => $currentGeneralPeriod->name
                ]);
            } else {
                // No active period - redirect to no available assessments
                $nextPeriod = AssessmentPeriod::getNextGeneralPeriod();

                if ($isDirector) {
                    session()->forget([
                        'assessment_district_id',
                        'assessment_lga_id',
                        'assessment_phc_id',
                        'assessment_location_selected'
                    ]);
                }

                return redirect()->route('assessments.no-available-assessments')
                    ->with('info', 'Your previous assessment for this location can no longer be edited, and a new assessment period has not been opened yet.')
                    ->with('next_date', $nextPeriod ? $nextPeriod->start_date : null);
            }
        }

        // If no active assessment period and no existing responses
        if (!$hasActiveAssessmentPeriod && $existingResponses->isEmpty()) {
            $nextPeriod = AssessmentPeriod::getNextGeneralPeriod();

            if ($isDirector) {
                session()->forget([
                    'assessment_district_id',
                    'assessment_lga_id',
                    'assessment_phc_id',
                    'assessment_location_selected'
                ]);
            }

            return redirect()->route('assessments.no-available-assessments')
                ->with('info', 'No assessment period is currently active.')
                ->with('next_date', $nextPeriod ? $nextPeriod->start_date : null);
        }

        if ($isNewAssessmentPeriod) {
            $existingResponsesMap = collect();
            $existingResponses = collect();
        }

        $existingResponses = collect();
        foreach ($existingResponsesMap as $assessmentId => $response) {
            $existingResponses[$assessmentId] = $response;
        }

        $sections = AssessmentSection::whereHas('assessments', function ($query) use ($assessments) {
            $query->whereIn('id', $assessments->pluck('id'));
        })->get();

        foreach ($assessments as $assessment) {
            if ($isNewAssessmentPeriod) {
                $assessment->has_response = false;
                $assessment->can_edit = true;
                $assessment->is_new_period = true;
            } else {
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

        if ($isNewAssessmentPeriod) {
            session()->flash('info', 'A new assessment period has been opened. Your previous responses are saved for reference, but you need to submit a new assessment.');
        }

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

    // public function store(Request $request)
    // {
    //     try {
    //         $user = Auth::user();
    //         $isDirector = $user->role && $user->role->name === 'director';

    //         // Get current assessment period
    //         $currentPeriod = AssessmentPeriod::getCurrentGeneralPeriod();
    //         if (!$currentPeriod) {
    //             return response()->json([
    //                 'success' => false,
    //                 'message' => 'No active assessment period found. Please contact the administrator.',
    //                 'redirect' => route('assessments.no-available-assessments')
    //             ], 400);
    //         }

    //         // Get PHC information
    //         if ($isDirector) {
    //             $phcId = session('assessment_phc_id');
    //             $districtId = session('assessment_district_id');
    //             $lgaId = session('assessment_lga_id');

    //             $district = session('assessment_district');
    //             $lga = session('assessment_lga');
    //             $phc = session('assessment_phc');
    //         } else {
    //             $phcId = $user->phc_id;
    //             $districtId = $user->phc->district_id ?? null;
    //             $lgaId = $user->phc->lga_id ?? null;

    //             $district = $user->phc->district ?? 'Unknown';
    //             $lga = $user->phc->lga ?? 'Unknown';
    //             $phc = $user->phc->name ?? 'Unknown';
    //         }

    //         if (!$phcId || !$districtId || !$lgaId) {
    //             return response()->json([
    //                 'success' => false,
    //                 'message' => 'PHC or location information is missing. Please select a PHC location.',
    //                 'redirect' => route('assessments.index')
    //             ], 400);
    //         }

    //         // Get all assessment questions
    //         $assessments = Assessment::with('section')->get();
    //         $responses = $request->input('responses', []);
    //         $staffResponses = $request->input('staff_responses', []);

    //         // Check if there are existing responses for this PHC in the current period
    //         $existingResponses = AssessmentResponse::where('phc_id', $phcId)
    //             ->where('assessment_period_id', $currentPeriod->id)
    //             ->get()
    //             ->keyBy('assessment_id');

    //         $hasExistingResponses = $existingResponses->isNotEmpty();

    //         // If this is an update attempt, check edit window
    //         if ($hasExistingResponses) {
    //             $editableUntil = $this->getEditableUntil($existingResponses->first()->created_at);
    //             $isWithinEditWindow = Carbon::now()->lessThanOrEqualTo($editableUntil);

    //             if (!$isWithinEditWindow) {
    //                 // Find unanswered questions
    //                 $unansweredQuestions = $this->findUnansweredQuestions($assessments, $existingResponses, $responses, $staffResponses);

    //                 Log::info('Update attempt outside of edit window', [
    //                     'user_id' => $user->id,
    //                     'is_director' => $isDirector,
    //                     'phc_id' => $phcId,
    //                     'edit_window_closed' => $editableUntil->toDateTimeString(),
    //                     'unanswered_questions' => $unansweredQuestions,
    //                     'unanswered_count' => count($unansweredQuestions)
    //                 ]);

    //                 // Return detailed error with specific questions
    //                 return response()->json([
    //                     'success' => false,
    //                     'message' => 'Assessment editing period has expired, but there are unanswered questions.',
    //                     'error_type' => 'edit_window_expired',
    //                     'edit_window_closed' => $editableUntil->format('M j, Y g:i A'),
    //                     'unanswered_questions' => $unansweredQuestions,
    //                     'unanswered_count' => count($unansweredQuestions),
    //                     'can_create_new' => $this->canCreateNewAssessment(),
    //                     'redirect' => route('dashboard')
    //                 ], 422);
    //             }
    //         }

    //         // Continue with normal processing...
    //         $this->saveAssessmentResponses($districtId, $lgaId, $phcId, $responses, $staffResponses, $user->id);

    //         return response()->json([
    //             'success' => true,
    //             'message' => 'Assessment saved successfully!',
    //             'redirect' => route('dashboard')
    //         ]);
    //     } catch (\Exception $e) {
    //         Log::error('Assessment submission error', [
    //             'user_id' => Auth::id(),
    //             'error' => $e->getMessage(),
    //             'trace' => $e->getTraceAsString()
    //         ]);

    //         return response()->json([
    //             'success' => false,
    //             'message' => 'An error occurred while saving your assessment. Please try again.',
    //             'redirect' => route('assessments.index')
    //         ], 500);
    //     }
    // }

    public function store(Request $request)
    {
        try {
            $user = Auth::user();
            $isDirector = $user->role && $user->role->name === 'director';

            // Get the admin-set assessment period
            $adminSetPeriod = AssessmentPeriod::getCurrentGeneralPeriod();
            if (!$adminSetPeriod) {
                Log::error('No admin-set assessment period found', [
                    'user_id' => $user->id,
                    'timestamp' => now()
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'No active assessment period found. Please contact the administrator to set up an assessment period.',
                    'redirect' => route('assessments.no-available-assessments')
                ], 400);
            }

            // Log the admin-set period details
            Log::info('Using admin-set assessment period', [
                'period_id' => $adminSetPeriod->id,
                'period_name' => $adminSetPeriod->name,
                'admin_set_quarter' => $adminSetPeriod->quarter,
                'admin_set_year' => $adminSetPeriod->year,
                'user_id' => $user->id
            ]);

            // Get PHC information
            if ($isDirector) {
                $phcId = session('assessment_phc_id');
                $districtId = session('assessment_district_id');
                $lgaId = session('assessment_lga_id');

                if (!$phcId || !$districtId || !$lgaId) {
                    return response()->json([
                        'success' => false,
                        'message' => 'PHC or location information is missing. Please select a PHC location.',
                        'redirect' => route('assessments.index')
                    ], 400);
                }
            } else {
                $phcId = $user->phc_id;
                $districtId = $user->phc->district_id ?? null;
                $lgaId = $user->phc->lga_id ?? null;

                if (!$phcId || !$districtId || !$lgaId) {
                    return response()->json([
                        'success' => false,
                        'message' => 'PHC or location information is missing.',
                        'redirect' => route('assessments.index')
                    ], 400);
                }
            }

            $responses = $request->input('responses', []);
            $staffResponses = $request->input('staff_responses', []);

            // Check for existing responses in this admin-set period
            $existingResponses = AssessmentResponse::where('phc_id', $phcId)
                ->where('assessment_period_id', $adminSetPeriod->id)
                ->get()
                ->keyBy('assessment_id');

            $hasExistingResponses = $existingResponses->isNotEmpty();

            // Check edit window if updating existing responses
            if ($hasExistingResponses) {
                $editableUntil = $this->getEditableUntil($existingResponses->first()->created_at);
                $isWithinEditWindow = Carbon::now()->lessThanOrEqualTo($editableUntil);

                if (!$isWithinEditWindow) {
                    Log::warning('Edit window expired for assessment period', [
                        'period_id' => $adminSetPeriod->id,
                        'period_name' => $adminSetPeriod->name,
                        'edit_window_closed' => $editableUntil->toDateTimeString(),
                        'user_id' => $user->id
                    ]);

                    return response()->json([
                        'success' => false,
                        'message' => 'Assessment editing period has expired for this assessment period.',
                        'error_type' => 'edit_window_expired',
                        'edit_window_closed' => $editableUntil->format('M j, Y g:i A'),
                        'redirect' => route('dashboard')
                    ], 422);
                }
            }

            // Save responses with admin-set period data
            $this->saveAssessmentResponsesWithAdminPeriod(
                $districtId,
                $lgaId,
                $phcId,
                $responses,
                $staffResponses,
                $user->id,
                $adminSetPeriod
            );

            // 🔥 CLEAR DIRECTOR SESSION AFTER SUCCESSFUL SUBMISSION
            if ($isDirector) {
                session()->forget([
                    'assessment_district_id',
                    'assessment_lga_id',
                    'assessment_phc_id',
                    'assessment_location_selected'
                ]);

                Log::info('Director session cleared after successful assessment submission', [
                    'user_id' => $user->id,
                    'phc_id' => $phcId,
                    'period_id' => $adminSetPeriod->id
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Assessment saved successfully for ' . $adminSetPeriod->name . '!',
                'redirect' => route('dashboard')
            ]);
        } catch (\Exception $e) {
            Log::error('Assessment submission error', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while saving your assessment. Please try again.',
                'redirect' => route('assessments.index')
            ], 500);
        }
    }

    /**
     * Find questions that haven't been answered yet
     */
    private function findUnansweredQuestions($assessments, $existingResponses, $newResponses, $newStaffResponses)
    {
        $unansweredQuestions = [];
        $questionCounter = 1;

        foreach ($assessments as $assessment) {
            $hasExistingResponse = isset($existingResponses[$assessment->id]);
            $hasNewResponse = isset($newResponses[$assessment->id]);
            $hasNewStaffResponse = isset($newStaffResponses[$assessment->id]);

            // Check if this question is answered
            $isAnswered = $hasExistingResponse || $hasNewResponse || $hasNewStaffResponse;

            if (!$isAnswered) {
                $sectionName = $assessment->section ? $assessment->section->section_name : 'Unknown Section';

                $unansweredQuestions[] = [
                    'question_number' => $questionCounter,
                    'assessment_id' => $assessment->id,
                    'question' => $assessment->question,
                    'section' => $sectionName,
                    'response_type' => $assessment->response_type
                ];
            }

            $questionCounter++;
        }

        return $unansweredQuestions;
    }

    // private function saveAssessmentResponses($districtId, $lgaId, $phcId, $responses, $staffResponses, $userId)
    // {
    //     $currentPeriod = AssessmentPeriod::getCurrentGeneralPeriod();

    //     if (!$currentPeriod) {
    //         throw new \Exception('No active assessment period found');
    //     }

    //     foreach ($responses as $assessmentId => $response) {
    //         if ($response !== null && $response !== '') {
    //             AssessmentResponse::updateOrCreate(
    //                 [
    //                     'assessment_id' => $assessmentId,
    //                     'district_id' => $districtId,
    //                     'lga_id' => $lgaId,
    //                     'phc_id' => $phcId,
    //                     'user_id' => $userId,
    //                     'assessment_period_id' => $currentPeriod->id
    //                 ],
    //                 [
    //                     'response' => is_array($response) ? json_encode($response) : $response,
    //                     'district_id' => $districtId,
    //                     'lga_id' => $lgaId,
    //                     'phc_id' => $phcId,
    //                     'user_id' => $userId,
    //                     'quarter' => $currentPeriod->quarter,
    //                     'year' => $currentPeriod->year,
    //                     'submitted_at' => now(),
    //                     'is_final_submission' => true,
    //                     'created_at' => now(),
    //                     'updated_at' => now()
    //                 ]
    //             );
    //         }
    //     }

    //     // Save staff responses
    //     foreach ($staffResponses as $assessmentId => $staffData) {
    //         if (is_array($staffData) && array_filter($staffData)) {
    //             AssessmentResponse::updateOrCreate(
    //                 [
    //                     'assessment_id' => $assessmentId,
    //                     'district_id' => $districtId,
    //                     'lga_id' => $lgaId,
    //                     'phc_id' => $phcId,
    //                     'user_id' => $userId,
    //                     'assessment_period_id' => $currentPeriod->id
    //                 ],
    //                 [
    //                     'response' => json_encode($staffData),
    //                     'user_id' => $userId,
    //                     'district_id' => $districtId,
    //                     'lga_id' => $lgaId,
    //                     'phc_id' => $phcId,
    //                     'quarter' => $currentPeriod->quarter,
    //                     'year' => $currentPeriod->year,
    //                     'submitted_at' => now(),
    //                     'is_final_submission' => true,
    //                     'created_at' => now(),
    //                     'updated_at' => now()
    //                 ]
    //             );
    //         }
    //     }
    // }

    private function saveAssessmentResponsesWithAdminPeriod($districtId, $lgaId, $phcId, $responses, $staffResponses, $userId, $adminSetPeriod)
    {
        // Extract admin-set values
        $adminQuarter = $adminSetPeriod->quarter;
        $adminYear = $adminSetPeriod->year;
        $periodId = $adminSetPeriod->id;

        Log::info('Saving responses with admin-set period data', [
            'period_id' => $periodId,
            'period_name' => $adminSetPeriod->name,
            'admin_quarter' => $adminQuarter,
            'admin_year' => $adminYear,
            'user_id' => $userId,
            'phc_id' => $phcId,
            'total_responses' => count($responses),
            'total_staff_responses' => count($staffResponses)
        ]);

        // Validate admin-set values
        if (empty($adminQuarter) || empty($adminYear)) {
            Log::error('Admin-set period missing quarter or year', [
                'period_id' => $periodId,
                'quarter' => $adminQuarter,
                'year' => $adminYear
            ]);
            throw new \Exception('Admin-set assessment period is missing quarter or year information. Please contact administrator.');
        }

        // Process regular responses
        foreach ($responses as $assessmentId => $response) {
            if ($response !== null && $response !== '') {
                try {
                    $responseData = [
                        'assessment_id' => (int)$assessmentId,
                        'district_id' => (int)$districtId,
                        'lga_id' => (int)$lgaId,
                        'phc_id' => (int)$phcId,
                        'user_id' => (int)$userId,
                        'assessment_period_id' => (int)$periodId,
                        'quarter' => $adminQuarter,  // From admin-set period
                        'year' => (int)$adminYear,   // From admin-set period
                        'response' => is_array($response) ? json_encode($response) : $response,
                        'submitted_at' => now(),
                        'is_final_submission' => true,
                        'updated_at' => now()
                    ];

                    Log::info('Saving individual response', [
                        'assessment_id' => $assessmentId,
                        'quarter' => $adminQuarter,
                        'year' => $adminYear,
                        'period_id' => $periodId
                    ]);

                    $savedResponse = AssessmentResponse::updateOrCreate(
                        [
                            'assessment_id' => (int)$assessmentId,
                            'district_id' => (int)$districtId,
                            'lga_id' => (int)$lgaId,
                            'phc_id' => (int)$phcId,
                            'user_id' => (int)$userId,
                            'assessment_period_id' => (int)$periodId
                        ],
                        $responseData
                    );

                    // Verify the save worked
                    $verification = AssessmentResponse::find($savedResponse->id);
                    Log::info('Response save verification', [
                        'response_id' => $savedResponse->id,
                        'saved_assessment_id' => $verification->assessment_id,
                        'saved_quarter' => $verification->quarter,
                        'saved_year' => $verification->year,
                        'saved_period_id' => $verification->assessment_period_id
                    ]);
                } catch (\Exception $e) {
                    Log::error('Failed to save individual response', [
                        'assessment_id' => $assessmentId,
                        'error' => $e->getMessage(),
                        'admin_quarter' => $adminQuarter,
                        'admin_year' => $adminYear
                    ]);
                    throw $e;
                }
            }
        }

        // Process staff responses with same admin-set period data
        foreach ($staffResponses as $assessmentId => $staffData) {
            if (is_array($staffData) && array_filter($staffData)) {
                try {
                    $responseData = [
                        'assessment_id' => (int)$assessmentId,
                        'district_id' => (int)$districtId,
                        'lga_id' => (int)$lgaId,
                        'phc_id' => (int)$phcId,
                        'user_id' => (int)$userId,
                        'assessment_period_id' => (int)$periodId,
                        'quarter' => $adminQuarter,  // From admin-set period
                        'year' => (int)$adminYear,   // From admin-set period
                        'response' => json_encode($staffData),
                        'submitted_at' => now(),
                        'is_final_submission' => true,
                        'updated_at' => now()
                    ];

                    AssessmentResponse::updateOrCreate(
                        [
                            'assessment_id' => (int)$assessmentId,
                            'district_id' => (int)$districtId,
                            'lga_id' => (int)$lgaId,
                            'phc_id' => (int)$phcId,
                            'user_id' => (int)$userId,
                            'assessment_period_id' => (int)$periodId
                        ],
                        $responseData
                    );

                    Log::info('Staff response saved successfully', [
                        'assessment_id' => $assessmentId,
                        'quarter' => $adminQuarter,
                        'year' => $adminYear
                    ]);
                } catch (\Exception $e) {
                    Log::error('Failed to save staff response', [
                        'assessment_id' => $assessmentId,
                        'error' => $e->getMessage()
                    ]);
                    throw $e;
                }
            }
        }

        Log::info('All responses saved successfully with admin-set period data', [
            'period_id' => $periodId,
            'quarter' => $adminQuarter,
            'year' => $adminYear,
            'user_id' => $userId
        ]);
    }


    public function noAvailableAssessments()
    {
        $nextDate = session('next_date');
        $info = session('info', 'No assessments are currently available.');

        if (is_string($nextDate)) {
            $nextDate = \Carbon\Carbon::parse($nextDate);
        }

        // Try to get next period if no session data
        if (!$nextDate) {
            $nextPeriod = AssessmentPeriod::getNextGeneralPeriod();
            $nextDate = $nextPeriod ? $nextPeriod->start_date : null;
        }

        $district = null;
        $lga = null;
        $phc = null;

        if (session('assessment_location_selected')) {
            $districtId = session('assessment_district_id');
            $lgaId = session('assessment_lga_id');
            $phcId = session('assessment_phc_id');

            if ($districtId && $lgaId && $phcId) {
                $district = \App\Models\District::find($districtId)->name ?? 'Unknown';
                $lga = \App\Models\Lga::find($lgaId)->name ?? 'Unknown';
                $phc = \App\Models\Phc::find($phcId)->name ?? 'Unknown';
            }
        }

        return view('assessments.no-available-assessments', [
            'title' => 'No Assessments Available',
            'message' => $info,
            'nextAvailableDate' => $nextDate,
            'daysRemaining' => $nextDate ? now()->diffInDays($nextDate) : null,
            'district' => $district,
            'lga' => $lga,
            'phc' => $phc
        ]);
    }

    private function canCreateNewAssessment()
    {
        return AssessmentPeriod::getCurrentGeneralPeriod() !== null;
    }

    private function getEditableUntil($createdAt)
    {
        return $createdAt->addWeek();
    }


    private function validateResponses($responses, $staffResponses, $assessments)
    {
        $errors = [];

        // Validate regular responses
        foreach ($responses as $assessmentId => $responseValue) {
            $assessment = $assessments->get($assessmentId);
            if (!$assessment) continue;

            $fieldKey = "responses.{$assessmentId}";
            $error = $this->validateSingleResponse($assessment, $responseValue, $fieldKey);
            if ($error) {
                $errors[$fieldKey] = [$error];
            }
        }

        // Validate staff responses
        foreach ($staffResponses as $assessmentId => $staffData) {
            $assessment = $assessments->get($assessmentId);
            if (!$assessment || $assessment->response_type !== 'form') continue;

            if (!is_array($staffData)) {
                $errors["staff_responses.{$assessmentId}"] = ['Staff data must be an array'];
                continue;
            }

            // Validate each staff category
            foreach (['full_time', 'contract', 'nysc_intern'] as $staffType) {
                $value = $staffData[$staffType] ?? 0;
                if (!is_numeric($value) || $value < 0) {
                    $errors["staff_responses.{$assessmentId}.{$staffType}"] = ["Must be a non-negative number"];
                }
            }
        }

        return $errors;
    }

    private function validateSingleResponse($assessment, $responseValue, $fieldKey)
    {
        switch ($assessment->response_type) {
            case 'int':
                if (!is_numeric($responseValue) || $responseValue < 0) {
                    return "Must be a non-negative number";
                }
                break;

            case 'year':
                $year = intval($responseValue);
                if ($year < 1960 || $year > date('Y')) {
                    return "Must be a valid year between 1960 and " . date('Y');
                }
                break;

            case 'text':
                if (empty(trim($responseValue))) {
                    return "This field is required";
                }
                if (strlen($responseValue) > 1000) {
                    return "Response cannot exceed 1000 characters";
                }
                break;

            case 'yes_no':
                if (!in_array($responseValue, ['yes', 'no', 'n/a'])) {
                    return "Must select Yes, No, or Not Applicable";
                }
                break;

            case 'good_bad':
                if (!in_array($responseValue, ['good', 'bad'])) {
                    return "Must select Good or Bad";
                }
                break;

            case 'select-category':
                $validOptions = ['CHC/Flagship', 'PHC', 'PHCc', 'HP'];
                if (!in_array($responseValue, $validOptions)) {
                    return "Must select a valid category";
                }
                break;

            case 'select-days-operation':
                $validOptions = ['5 days 8 hours', '7 days 24 hours'];
                if (!in_array($responseValue, $validOptions)) {
                    return "Must select a valid operation schedule";
                }
                break;

            case 'select-water-source':
                $validOptions = ['Borehole', 'Well', 'None'];
                if (!in_array($responseValue, $validOptions)) {
                    return "Must select a valid water source";
                }
                break;

            case 'select-frequency':
                $validOptions = ['Weekly', 'Bi-Weekly', 'Monthly', 'Bi-Monthly', 'Quarterly', 'Annually', 'Not Treated', 'None'];
                if (!in_array($responseValue, $validOptions)) {
                    return "Must select a valid frequency";
                }
                break;

            case 'select-multiple-services':
            case 'select-multiple-pharmacy':
            case 'select-multiple-emergency':
                if (!is_array($responseValue) || empty($responseValue)) {
                    return "At least one option must be selected";
                }
                break;

            case 'select':
                // For general select type with options from database
                if ($assessment->options && is_array($assessment->options)) {
                    if (!in_array($responseValue, $assessment->options)) {
                        return "Must select a valid option";
                    }
                }
                break;
        }

        return null;
    }

    public function saveTemporary(Request $request)
    {
        try {
            $user = auth()->user();
            $phcId = $request->input('phc_id');
            $responses = $request->input('responses', []);
            $staffResponses = $request->input('staff_responses', []);
            $currentPage = $request->input('current_page', 0);

            if (!$phcId) {
                return response()->json([
                    'success' => false,
                    'message' => 'PHC ID is required'
                ], 400);
            }

            $allResponses = [
                'responses' => $responses,
                'staff_responses' => $staffResponses
            ];

            $tempAssessment = TemporaryAssessment::where('user_id', $user->id)
                ->where('phc_id', $phcId)
                ->first();

            if ($tempAssessment) {
                $tempAssessment->update([
                    'responses' => json_encode($allResponses),
                    'current_page' => $currentPage,
                    'updated_at' => now()
                ]);
            } else {
                TemporaryAssessment::create([
                    'user_id' => $user->id,
                    'phc_id' => $phcId,
                    'responses' => json_encode($allResponses),
                    'current_page' => $currentPage,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Progress saved successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('Error saving temporary responses: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to save progress: ' . $e->getMessage()
            ], 500);
        }
    }

    public function loadTemporary($phcId)
    {
        try {
            $user = auth()->user();

            $tempAssessment = TemporaryAssessment::where('user_id', $user->id)
                ->where('phc_id', $phcId)
                ->first();

            if ($tempAssessment) {
                $responseData = json_decode($tempAssessment->responses, true);

                return response()->json([
                    'success' => true,
                    'data' => [
                        'responses' => $responseData['responses'] ?? [],
                        'staff_responses' => $responseData['staff_responses'] ?? [],
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

    private function jsonResponse($success, $message, $redirect = null)
    {
        $response = [
            'success' => $success,
            'message' => $message
        ];

        if ($redirect) {
            $response['redirect'] = $redirect;
        }

        return response()->json($response, $success ? 200 : 422);
    }

    public function getChildQuestions(Assessment $assessment, Request $request)
    {
        $user = auth()->user();
        $selectedOption = $request->input('selected_option');

        if ($selectedOption == 'yes') {
            $conditionPrefix = 'if yes';
        } else if ($selectedOption == 'no') {
            $conditionPrefix = 'if no';
        } else if ($selectedOption == 'n/a') {
            $conditionPrefix = 'if n/a';
        }

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

    // public function noAvailableAssessments()
    // {
    //     $nextDate = session('next_date');
    //     $info = session('info', 'No assessments are currently available.');

    //     if (is_string($nextDate)) {
    //         $nextDate = \Carbon\Carbon::parse($nextDate);
    //     }

    //     $district = null;
    //     $lga = null;
    //     $phc = null;

    //     if (session('assessment_location_selected')) {
    //         $districtId = session('assessment_district_id');
    //         $lgaId = session('assessment_lga_id');
    //         $phcId = session('assessment_phc_id');

    //         if ($districtId && $lgaId && $phcId) {
    //             $district = \App\Models\District::find($districtId)->name ?? 'Unknown';
    //             $lga = \App\Models\Lga::find($lgaId)->name ?? 'Unknown';
    //             $phc = \App\Models\Phc::find($phcId)->name ?? 'Unknown';
    //         }
    //     }

    //     return view('assessments.no-available-assessments', [
    //         'title' => 'No Assessments Available',
    //         'message' => $info,
    //         'nextAvailableDate' => $nextDate,
    //         'daysRemaining' => $nextDate ? now()->diffInDays($nextDate) : null,
    //         'district' => $district,
    //         'lga' => $lga,
    //         'phc' => $phc
    //     ]);
    // }


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
        $staffResponses = $request->input('staff_responses', []);

        $isDirector = $user->role->name === 'director' || $user->role->name === 'Director';

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

        // Get current assessment period
        $currentPeriod = AssessmentPeriod::getCurrentGeneralPeriod();
        if (!$currentPeriod) {
            return redirect()->route('assessments.no-available-assessments')
                ->with('error', 'No active assessment period found.');
        }

        $oneWeekAgo = now()->subWeek();
        $canEdit = true;
        $nonEditableResponses = [];

        // Check both regular and staff responses for edit window
        $allResponses = array_merge($responses, $staffResponses);

        foreach ($allResponses as $assessmentId => $responseValue) {
            $query = AssessmentResponse::where('user_id', $user->id)
                ->where('assessment_id', $assessmentId)
                ->where('assessment_period_id', $currentPeriod->id);

            if ($isDirector) {
                $query->where('phc_id', session('assessment_phc_id'));
            }

            $existingResponse = $query->first();

            if (!$existingResponse) {
                $nonEditableResponses[] = "Question #$assessmentId (no previous response)";
                $canEdit = false;
                continue;
            }

            if ($existingResponse->created_at->lt($oneWeekAgo)) {
                $assessment = Assessment::find($assessmentId);
                $questionText = $assessment ? $assessment->question : "Question #$assessmentId";
                $nonEditableResponses[] = $questionText;
                $canEdit = false;
            }
        }

        if (!$canEdit) {
            Log::info('Update attempt outside of edit window', [
                'user_id' => $user->id,
                'is_director' => $isDirector,
                'non_editable' => $nonEditableResponses
            ]);

            return redirect()->route('assessments.no-available-assessments')
                ->with('info', 'Your assessment can no longer be edited as it has been more than a week since submission.');
        }

        $successCount = 0;
        $errorCount = 0;

        DB::beginTransaction();

        try {
            // Update regular responses
            foreach ($responses as $assessmentId => $responseValue) {
                $query = AssessmentResponse::where('user_id', $user->id)
                    ->where('assessment_id', $assessmentId)
                    ->where('assessment_period_id', $currentPeriod->id);

                if ($isDirector) {
                    $query->where('phc_id', session('assessment_phc_id'));
                }

                $existingResponse = $query->first();

                if (!$existingResponse) {
                    continue;
                }

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
            }

            // Update staff responses
            foreach ($staffResponses as $assessmentId => $staffData) {
                $query = AssessmentResponse::where('user_id', $user->id)
                    ->where('assessment_id', $assessmentId)
                    ->where('assessment_period_id', $currentPeriod->id);

                if ($isDirector) {
                    $query->where('phc_id', session('assessment_phc_id'));
                }

                $existingResponse = $query->first();

                if (!$existingResponse) {
                    continue;
                }

                $existingResponse->update([
                    'response' => json_encode($staffData),
                    'updated_at' => now()
                ]);

                Log::info('Updated staff response', [
                    'assessment_id' => $assessmentId,
                    'response_id' => $existingResponse->id,
                    'is_director' => $isDirector
                ]);

                $successCount++;
            }

            DB::commit();

            // 🔥 CLEAR DIRECTOR SESSION AFTER SUCCESSFUL UPDATE
            if ($isDirector) {
                session()->forget([
                    'assessment_district_id',
                    'assessment_lga_id',
                    'assessment_phc_id',
                    'assessment_location_selected'
                ]);

                Log::info('Director session cleared after successful assessment update', [
                    'user_id' => $user->id,
                    'success_count' => $successCount
                ]);
            }

            $message = 'Assessment responses updated successfully';

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => $message,
                    'redirect' => route('dashboard')
                ]);
            }

            return redirect()->route('dashboard')->with('success', $message);
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Failed to update assessment responses: ' . $e->getMessage());

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to update assessment responses. Please try again.'
                ], 500);
            }

            return redirect()->back()->with('error', 'Failed to update assessment responses. Please try again.');
        }
    }

    public function debugAssessmentPeriod()
    {
        $currentPeriod = AssessmentPeriod::getCurrentGeneralPeriod();

        if ($currentPeriod) {
            dd([
                'period_id' => $currentPeriod->id,
                'period_name' => $currentPeriod->name,
                'quarter' => $currentPeriod->quarter,
                'year' => $currentPeriod->year,
                'start_date' => $currentPeriod->start_date,
                'end_date' => $currentPeriod->end_date,
                'all_attributes' => $currentPeriod->toArray()
            ]);
        } else {
            dd('No current assessment period found');
        }
    }

    public function debugAdminSetPeriod()
    {
        $currentPeriod = AssessmentPeriod::getCurrentGeneralPeriod();

        if ($currentPeriod) {
            dd([
                'Admin Set Period Details:',
                'id' => $currentPeriod->id,
                'name' => $currentPeriod->name,
                'quarter (from admin)' => $currentPeriod->quarter,
                'year (from admin)' => $currentPeriod->year,
                'start_date' => $currentPeriod->start_date,
                'end_date' => $currentPeriod->end_date,
                'status' => $currentPeriod->status ?? 'no status',
                'created_by_admin' => $currentPeriod->created_by ?? 'unknown',
                'all_data' => $currentPeriod->toArray()
            ]);
        } else {
            dd('No active assessment period found - Admin needs to set one');
        }
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
                return 91;
            case 'monthly':
                return 30;
            case 'bi-annually':
                return 182;
            case 'annually':
                return 365;
            case 'custom':
                return $customIntervalDays ?? 91;
            default:
                return 91;
        }
    }

    private function clearTemporaryResponses($phcId)
    {
        try {
            $user = auth()->user();

            TemporaryAssessment::where('user_id', $user->id)
                ->where('phc_id', $phcId)
                ->delete();

            return true;
        } catch (\Exception $e) {
            Log::error('Error clearing temporary responses: ' . $e->getMessage());
            return false;
        }
    }

    public function getDistricts()
    {
        $districts = District::all();
        return response()->json($districts);
    }

    public function getLocalGovernments($districtId)
    {
        $localGovernments = Lga::where('district_id', $districtId)->get();
        return response()->json($localGovernments);
    }

    public function getPHCs($lgaId)
    {
        $phcs = PHC::where('lga_id', $lgaId)->get();
        return response()->json($phcs);
    }

    public function getAssessmentSections()
    {
        $sections = AssessmentSection::with('assessments')->get();
        return response()->json($sections);
    }

    // private function getEditableUntil($createdAt)
    // {
    //     return $createdAt->addWeek();
    // }

    // private function canCreateNewAssessment($phcId)
    // {
    //     $now = now();
    //     return Assessment::whereNotNull('next_available_date')
    //         ->where('next_available_date', '<=', $now)
    //         ->exists();
    // }
}

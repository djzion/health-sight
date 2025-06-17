<?php

namespace App\Http\Controllers;

use App\Models\District;
use App\Models\Lga;
use App\Models\Phc;
use App\Models\Qip;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use App\Models\Safecare;
use App\Models\SafecareAssessment;
use App\Models\SafecareResponses;
use App\Models\SafecarePeriod; // New model for managing assessment periods
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class QipController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $role = $user->role;
        $now = now();
        $oneWeekAgo = $now->copy()->subWeek();

        // Check if user is director/admin
        $isDirector = in_array($role->name, ['director', 'Director', 'admin', 'Admin']);

        $districts = District::all();
        $lgas = Lga::all();
        $phcs = Phc::all();

        // Check for active SafeCare assessment period
        $activePeriod = SafecarePeriod::where('start_date', '<=', $now)
            ->where('end_date', '>=', $now)
            ->where('is_active', true)
            ->first();

        // Check if there's a new assessment period available
        $newAssessmentPeriod = SafecarePeriod::where('start_date', '<=', $now)
            ->where('is_active', true)
            ->whereDoesntHave('safecareAssessments', function($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->exists();

        $canAccessAssessment = $activePeriod !== null;
        $nextAvailableDate = null;
        $editWindowExpired = false;
        $canEdit = true;

        if (!$canAccessAssessment) {
            // Get next scheduled period
            $nextPeriod = SafecarePeriod::where('start_date', '>', $now)
                ->where('is_active', true)
                ->orderBy('start_date')
                ->first();

            if ($nextPeriod) {
                $nextAvailableDate = $nextPeriod->start_date;
            }
        } else {
            // Check existing assessments and edit permissions
            $existingAssessments = SafecareAssessment::where('user_id', $user->id);

            if ($activePeriod) {
                $existingAssessments->where('created_at', '>=', $activePeriod->start_date)
                                  ->where('created_at', '<=', $activePeriod->end_date);
            }

            $existingAssessments = $existingAssessments->get();

            if ($existingAssessments->isNotEmpty()) {
                $oldestAssessment = $existingAssessments->sortBy('created_at')->first();
                $canEdit = $oldestAssessment->created_at->gt($oneWeekAgo);
                $editWindowExpired = !$canEdit;

                if ($editWindowExpired && !$newAssessmentPeriod) {
                    return view('qip.no-available-assessment', [
                        'nextAvailableDate' => $nextAvailableDate,
                        'info' => 'Your previous SafeCare assessment can no longer be edited (7-day window expired), and no new assessment period is available.',
                        'activePeriod' => $activePeriod
                    ]);
                }
            }
        }

        return view('qip.index', compact(
            'districts',
            'lgas',
            'phcs',
            'canAccessAssessment',
            'nextAvailableDate',
            'activePeriod',
            'canEdit',
            'editWindowExpired',
            'newAssessmentPeriod',
            'isDirector'
        ));
    }

    public function getSafecareAssessment(Request $request)
    {
        try {
            $phcId = $request->phc_id;
            $lgaId = $request->lga_id;
            $districtId = $request->district_id;
            $user = auth()->user();
            $now = now();
            $oneWeekAgo = $now->copy()->subWeek();

            Log::info("Getting SafeCare assessment for PHC ID: {$phcId}");

            // Check if there's an active assessment period
            $activePeriod = SafecarePeriod::where('start_date', '<=', $now)
                ->where('end_date', '>=', $now)
                ->where('is_active', true)
                ->first();

            if (!$activePeriod) {
                return response()->json([
                    'error' => 'No active SafeCare assessment period. Please contact administrator.',
                    'can_access' => false
                ], 403);
            }

            // Get SafeCare questions
            $questions = Safecare::where('status', 'active')
                ->select('id', 'question_no', 'question_description', 'question_short', 'section', 'status')
                ->orderBy('section')
                ->orderBy('question_no')
                ->get();

            if ($questions->isEmpty()) {
                Log::warning('No active questions found in the safecare table');
                return response()->json([
                    'questions' => [],
                    'groupedQuestions' => [],
                    'error' => 'No active questions found'
                ]);
            }

            // Get the latest assessment for this PHC within the current period
            $latestAssessment = SafecareAssessment::where('phc_id', $phcId)
                ->where('user_id', $user->id) // Only current user's assessments
                ->where('created_at', '>=', $activePeriod->start_date)
                ->where('created_at', '<=', $activePeriod->end_date)
                ->with(['user:id,full_name', 'updatedBy:id,full_name'])
                ->orderBy('assessment_date', 'desc')
                ->first();

            // Check if user can edit existing assessment
            $canEdit = true;
            $editWindowExpired = false;
            $isNewAssessmentPeriod = false;

            if ($latestAssessment) {
                $canEdit = $latestAssessment->created_at->gt($oneWeekAgo);
                $editWindowExpired = !$canEdit;

                // Check if there's a newer period available for new submission
                $newerPeriod = SafecarePeriod::where('start_date', '>', $latestAssessment->created_at)
                    ->where('start_date', '<=', $now)
                    ->where('end_date', '>=', $now)
                    ->where('is_active', true)
                    ->exists();

                if ($editWindowExpired && $newerPeriod) {
                    $isNewAssessmentPeriod = true;
                    $canEdit = true; // Can submit new assessment for new period
                    $latestAssessment = null; // Don't pre-populate for new period
                }
            }

            Log::info("Assessment edit status", [
                'can_edit' => $canEdit,
                'edit_window_expired' => $editWindowExpired,
                'is_new_period' => $isNewAssessmentPeriod,
                'has_existing_assessment' => $latestAssessment !== null
            ]);

            // Build safecare_result data structure
            $safecareResult = null;
            if ($latestAssessment && !$isNewAssessmentPeriod) {
                $safecareLevel = $this->calculateSafeCareLevel($latestAssessment->compliance_percentage);

                $safecareResult = [
                    'id' => $latestAssessment->id,
                    'safecare_level' => $safecareLevel,
                    'safecare_score' => $latestAssessment->compliance_percentage,
                    'compliance_percentage' => $latestAssessment->compliance_percentage,
                    'assessment_date' => $latestAssessment->assessment_date,
                    'created_at' => $latestAssessment->created_at,
                    'updated_at' => $latestAssessment->updated_at,
                    'last_updated_date' => $latestAssessment->last_updated_date,
                    'assessor_name' => $latestAssessment->user ? $latestAssessment->user->full_name : 'Unknown',
                    'user_name' => $latestAssessment->user ? $latestAssessment->user->full_name : 'Unknown',
                    'updated_by_name' => $latestAssessment->updatedBy ? $latestAssessment->updatedBy->full_name : null,
                    'total_questions' => $latestAssessment->total_questions,
                    'fully_compliant' => $latestAssessment->fully_compliant_count,
                    'partially_compliant' => $latestAssessment->partially_compliant_count,
                    'not_compliant' => $latestAssessment->not_compliant_count,
                    'not_applicable' => $latestAssessment->not_applicable_count,
                    'has_been_updated' => $latestAssessment->updated_by !== null,
                    'can_edit' => $canEdit,
                    'edit_window_expired' => $editWindowExpired
                ];
            }

            // Get previous responses for pre-populating form
            $previousResponses = null;
            if ($latestAssessment && !$isNewAssessmentPeriod) {
                $previousResponses = $this->getPreviousResponses($latestAssessment, $questions);
            }

            $groupedQuestions = $questions->groupBy('section');

            return response()->json([
                'questions' => $questions,
                'groupedQuestions' => $groupedQuestions,
                'previousAssessment' => $previousResponses,
                'previousAssessmentDate' => $latestAssessment ? $latestAssessment->assessment_date->format('F j, Y') : null,
                'safecare_result' => $safecareResult,
                'has_previous_assessment' => $latestAssessment !== null && !$isNewAssessmentPeriod,
                'can_edit' => $canEdit,
                'edit_window_expired' => $editWindowExpired,
                'is_new_assessment_period' => $isNewAssessmentPeriod,
                'active_period' => $activePeriod ? [
                    'id' => $activePeriod->id,
                    'name' => $activePeriod->name,
                    'start_date' => $activePeriod->start_date,
                    'end_date' => $activePeriod->end_date,
                    'quarter' => $activePeriod->quarter,
                    'year' => $activePeriod->year
                ] : null
            ]);
        } catch (\Exception $e) {
            Log::error('Error in getSafecareAssessment: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            return response()->json([
                'error' => 'An error occurred while fetching assessment questions: ' . $e->getMessage()
            ], 500);
        }
    }

    public function saveSafecareAssessment(Request $request)
    {
        try {
            $user = auth()->user();
            $now = now();
            $oneWeekAgo = $now->copy()->subWeek();

            Log::info('saveSafecareAssessment called with params: ' . json_encode($request->all()));

            $data = $request->json()->all();
            if (empty($data)) {
                $data = $request->all();
            }

            $districtId = $data['district_id'] ?? $request->district_id;
            $lgaId = $data['lga_id'] ?? $request->lga_id;
            $phcId = $data['phc_id'] ?? $request->phc_id;
            $responses = $data['responses'] ?? $request->responses;
            $isNewPeriod = $data['is_new_period'] ?? false;

            // Validate required data
            if (!$districtId || !$lgaId || !$phcId || !$responses) {
                return response()->json([
                    'success' => false,
                    'message' => 'Missing required data'
                ], 422);
            }

            // Check if there's an active assessment period
            $activePeriod = SafecarePeriod::where('start_date', '<=', $now)
                ->where('end_date', '>=', $now)
                ->where('is_active', true)
                ->first();

            if (!$activePeriod) {
                return response()->json([
                    'success' => false,
                    'message' => 'No active SafeCare assessment period available.'
                ], 403);
            }

            // Check for existing assessment in current period
            $existingAssessment = SafecareAssessment::where('user_id', $user->id)
                ->where('phc_id', $phcId)
                ->where('created_at', '>=', $activePeriod->start_date)
                ->where('created_at', '<=', $activePeriod->end_date)
                ->first();

            // Validate edit permissions
            if ($existingAssessment && !$isNewPeriod) {
                $canEdit = $existingAssessment->created_at->gt($oneWeekAgo);
                if (!$canEdit) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Assessment can no longer be edited. 7-day edit window has expired.'
                    ], 403);
                }

                // Update existing assessment
                return $this->updateExistingAssessment($existingAssessment, $responses, $activePeriod);
            }

            // Create new assessment
            $assessmentData = [
                'user_id' => $user->id,
                'district_id' => $districtId,
                'lga_id' => $lgaId,
                'phc_id' => $phcId,
                'assessment_date' => now(),
                'total_questions' => count($responses),
                'fully_compliant_count' => 0,
                'partially_compliant_count' => 0,
                'not_compliant_count' => 0,
                'not_applicable_count' => 0,
                'safecare_period_id' => $activePeriod->id,
                'quarter' => $activePeriod->quarter,
                'year' => $activePeriod->year,
            ];

            // Process responses and calculate compliance
            $this->processAssessmentResponses($assessmentData, $responses);

            // Save the assessment
            $assessment = SafecareAssessment::create($assessmentData);
            $assessment->load('user:id,full_name');

            $summary = [
                'total_questions' => $assessment->total_questions,
                'fully_compliant' => $assessment->fully_compliant_count,
                'partially_compliant' => $assessment->partially_compliant_count,
                'not_compliant' => $assessment->not_compliant_count,
                'not_applicable' => $assessment->not_applicable_count
            ];

            return response()->json([
                'success' => true,
                'message' => 'SafeCare Assessment successfully completed',
                'assessment_id' => $assessment->id,
                'compliance_percentage' => $assessment->compliance_percentage,
                'summary' => $summary,
                'tracking_info' => [
                    'assessment_date' => $assessment->assessment_date,
                    'created_at' => $assessment->created_at,
                    'assessor_name' => $assessment->user ? $assessment->user->full_name : 'Current User',
                    'is_new_assessment' => true,
                    'period' => $activePeriod->name,
                    'quarter' => $activePeriod->quarter,
                    'year' => $activePeriod->year
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error in saveSafecareAssessment: ' . $e->getMessage());
            Log::error($e->getTraceAsString());

            return response()->json([
                'success' => false,
                'message' => 'Server error: ' . $e->getMessage()
            ], 500);
        }
    }

     public function updateSafecareAssessment(Request $request)
    {
        try {
            DB::beginTransaction();

            Log::info('updateSafecareAssessment called with params: ' . json_encode($request->all()));

            // FIXED: Changed from safecare_questions to safecare (matching your model)
            $validatedData = $request->validate([
                'assessment_id' => 'required|exists:safecare_assessments,id',
                'district_id' => 'required|exists:districts,id',
                'lga_id' => 'required|exists:lgas,id',
                'phc_id' => 'required|exists:phcs,id',
                'responses' => 'required|array',
                'responses.*.question_id' => 'required|exists:safecare,id', // FIXED: was safecare_questions
                'responses.*.response' => 'required|in:FC,PC,NC,NA'
            ]);

            $assessmentId = $validatedData['assessment_id'];

            // Get the existing assessment with original assessor info
            $existingAssessment = SafecareAssessment::with(['user:id,full_name'])
                ->findOrFail($assessmentId);

            // Store original data for tracking
            $originalAssessmentDate = $existingAssessment->assessment_date;
            $originalCreatedAt = $existingAssessment->created_at;
            $originalAssessor = $existingAssessment->user;

            // Prepare updated assessment data
            $updateData = [
                'total_questions' => count($validatedData['responses']),
                'fully_compliant_count' => 0,
                'partially_compliant_count' => 0,
                'not_compliant_count' => 0,
                'not_applicable_count' => 0,
                'updated_by' => auth()->id(), // Track who updated
                'last_updated_date' => now(), // Track when it was updated
                // NOTE: assessment_date is NOT updated - it preserves the original assessment date
            ];

            // Get question mapping
            $questions = Safecare::all()->keyBy('id');

            // Clear existing question responses (set all q_ columns to null)
            foreach ($questions as $question) {
                $columnName = 'q_' . str_replace('.', '_', $question->question_no);
                $updateData[$columnName] = null;

                $commentColumn = 'comment_' . str_replace('.', '_', $question->question_no);
                $updateData[$commentColumn] = null;
            }

            // Process each new response
            foreach ($validatedData['responses'] as $response) {
                $questionId = $response['question_id'];
                $responseValue = $response['response'];

                // Get question details
                if (!isset($questions[$questionId])) {
                    continue;
                }

                $question = $questions[$questionId];
                $columnName = 'q_' . str_replace('.', '_', $question->question_no);

                // Set the response in the appropriate column
                $updateData[$columnName] = $responseValue;

                // Count compliance levels
                switch ($responseValue) {
                    case 'FC':
                        $updateData['fully_compliant_count']++;
                        break;
                    case 'PC':
                        $updateData['partially_compliant_count']++;
                        break;
                    case 'NC':
                        $updateData['not_compliant_count']++;
                        break;
                    case 'NA':
                        $updateData['not_applicable_count']++;
                        break;
                }
            }

            // Calculate updated compliance percentage
            $scoringQuestions = $updateData['fully_compliant_count'] +
                $updateData['partially_compliant_count'] +
                $updateData['not_compliant_count'];

            if ($scoringQuestions > 0) {
                $updateData['compliance_percentage'] = round(
                    (($updateData['fully_compliant_count'] * 100) +
                        ($updateData['partially_compliant_count'] * 50)) /
                        ($scoringQuestions * 100) * 100,
                    2
                );
            } else {
                $updateData['compliance_percentage'] = 0.00;
            }

            // Update the assessment
            $existingAssessment->update($updateData);

            // Refresh the model to get updated data and load relationships
            $existingAssessment->refresh();
            $existingAssessment->load(['user:id,full_name', 'updatedBy:id,full_name']);

            DB::commit();

            // Determine SafeCare level
            $safecareLevel = $this->calculateSafeCareLevel($updateData['compliance_percentage']);

            // Prepare response with enhanced tracking info
            return response()->json([
                'success' => true,
                'message' => 'SafeCare assessment updated successfully',
                'assessment_id' => $assessmentId,
                'compliance_percentage' => $updateData['compliance_percentage'],
                'safecare_level' => $safecareLevel,
                'summary' => [
                    'total_questions' => $updateData['total_questions'],
                    'fully_compliant' => $updateData['fully_compliant_count'],
                    'partially_compliant' => $updateData['partially_compliant_count'],
                    'not_compliant' => $updateData['not_compliant_count'],
                    'not_applicable' => $updateData['not_applicable_count']
                ],
                'tracking_info' => [
                    'assessment_date' => $originalAssessmentDate, // Preserve original assessment date
                    'created_at' => $originalCreatedAt, // When it was originally created
                    'updated_at' => $existingAssessment->updated_at, // Latest update timestamp
                    'last_updated_date' => $existingAssessment->last_updated_date, // Our custom update timestamp
                    'original_assessor' => $originalAssessor ? ($originalAssessor->full_name ?? $originalAssessor->name) : 'Unknown',
                    'original_assessor_id' => $existingAssessment->user_id,
                    'updated_by' => $existingAssessment->updatedBy ? ($existingAssessment->updatedBy->full_name ?? $existingAssessment->updatedBy->name) : auth()->user()->full_name ?? auth()->user()->name,
                    'updated_by_id' => auth()->id(),
                    'is_updated' => true,
                    'has_been_updated' => true
                ]
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating SafeCare assessment: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());

            return response()->json([
                'success' => false,
                'message' => 'Error updating assessment: ' . $e->getMessage()
            ], 500);
        }
    }

    private function updateExistingAssessment($assessment, $responses, $activePeriod)
    {
        try {
            DB::beginTransaction();

            // Prepare updated assessment data
            $updateData = [
                'total_questions' => count($responses),
                'fully_compliant_count' => 0,
                'partially_compliant_count' => 0,
                'not_compliant_count' => 0,
                'not_applicable_count' => 0,
                'updated_by' => auth()->id(),
                'last_updated_date' => now(),
            ];

            // Process responses and calculate compliance
            $this->processAssessmentResponses($updateData, $responses);

            // Update the assessment
            $assessment->update($updateData);
            $assessment->refresh();
            $assessment->load(['user:id,full_name', 'updatedBy:id,full_name']);

            DB::commit();

            $safecareLevel = $this->calculateSafeCareLevel($updateData['compliance_percentage']);

            return response()->json([
                'success' => true,
                'message' => 'SafeCare assessment updated successfully',
                'assessment_id' => $assessment->id,
                'compliance_percentage' => $updateData['compliance_percentage'],
                'safecare_level' => $safecareLevel,
                'summary' => [
                    'total_questions' => $updateData['total_questions'],
                    'fully_compliant' => $updateData['fully_compliant_count'],
                    'partially_compliant' => $updateData['partially_compliant_count'],
                    'not_compliant' => $updateData['not_compliant_count'],
                    'not_applicable' => $updateData['not_applicable_count']
                ],
                'tracking_info' => [
                    'assessment_date' => $assessment->assessment_date,
                    'created_at' => $assessment->created_at,
                    'updated_at' => $assessment->updated_at,
                    'last_updated_date' => $assessment->last_updated_date,
                    'original_assessor' => $assessment->user ? $assessment->user->full_name : 'Unknown',
                    'updated_by' => $assessment->updatedBy ? $assessment->updatedBy->full_name : auth()->user()->full_name,
                    'is_updated' => true,
                    'period' => $activePeriod->name
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating SafeCare assessment: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error updating assessment: ' . $e->getMessage()
            ], 500);
        }
    }

    private function processAssessmentResponses(&$assessmentData, $responses)
    {
        // Get question mapping
        $questions = Safecare::all()->keyBy('id');

        // Clear existing question responses for updates
        foreach ($questions as $question) {
            $columnName = 'q_' . str_replace('.', '_', $question->question_no);
            $assessmentData[$columnName] = null;

            $commentColumn = 'comment_' . str_replace('.', '_', $question->question_no);
            $assessmentData[$commentColumn] = null;
        }

        // Process each response
        foreach ($responses as $response) {
            if (!isset($response['question_id']) || !isset($response['response'])) {
                continue;
            }

            $questionId = $response['question_id'];
            $responseValue = $response['response'];

            if (!isset($questions[$questionId])) {
                continue;
            }

            $question = $questions[$questionId];
            $columnName = 'q_' . str_replace('.', '_', $question->question_no);

            $assessmentData[$columnName] = $responseValue;

            // Count compliance levels
            switch ($responseValue) {
                case 'FC':
                    $assessmentData['fully_compliant_count']++;
                    break;
                case 'PC':
                    $assessmentData['partially_compliant_count']++;
                    break;
                case 'NC':
                    $assessmentData['not_compliant_count']++;
                    break;
                case 'NA':
                    $assessmentData['not_applicable_count']++;
                    break;
            }

            // Add comment if exists
            if (!empty($response['comment'])) {
                $commentColumn = 'comment_' . str_replace('.', '_', $question->question_no);
                $assessmentData[$commentColumn] = $response['comment'];
            }
        }

        // Calculate compliance percentage
        $scoringQuestions = $assessmentData['fully_compliant_count'] +
            $assessmentData['partially_compliant_count'] +
            $assessmentData['not_compliant_count'];

        if ($scoringQuestions > 0) {
            $assessmentData['compliance_percentage'] = round(
                (($assessmentData['fully_compliant_count'] * 100) +
                    ($assessmentData['partially_compliant_count'] * 50)) /
                    ($scoringQuestions * 100) * 100,
                2
            );
        } else {
            $assessmentData['compliance_percentage'] = 0.00;
        }
    }

    // Admin methods for managing assessment periods
    public function createAssessmentPeriod(Request $request)
    {
        $user = auth()->user();

        // Check if user has admin privileges
        if (!in_array($user->role->name, ['admin', 'Admin', 'director', 'Director'])) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access'
            ], 403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'quarter' => 'required|in:Q1,Q2,Q3,Q4',
            'year' => 'required|integer|min:2024|max:2030',
            'description' => 'nullable|string'
        ]);

        try {
            $period = SafecarePeriod::create([
                'name' => $validated['name'],
                'start_date' => $validated['start_date'],
                'end_date' => $validated['end_date'],
                'quarter' => $validated['quarter'],
                'year' => $validated['year'],
                'description' => $validated['description'] ?? null,
                'is_active' => true,
                'created_by' => $user->id
            ]);

            return response()->json([
                'success' => true,
                'message' => 'SafeCare assessment period created successfully',
                'period' => $period
            ]);

        } catch (\Exception $e) {
            Log::error('Error creating assessment period: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error creating assessment period'
            ], 500);
        }
    }

    public function getAssessmentPeriods()
    {
        $periods = SafecarePeriod::orderBy('created_at', 'desc')->get();

        return response()->json([
            'success' => true,
            'periods' => $periods
        ]);
    }

    public function togglePeriodStatus(Request $request, $periodId)
    {
        $user = auth()->user();

        if (!in_array($user->role->name, ['admin', 'Admin', 'director', 'Director'])) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access'
            ], 403);
        }

        try {
            $period = SafecarePeriod::findOrFail($periodId);
            $period->is_active = !$period->is_active;
            $period->save();

            return response()->json([
                'success' => true,
                'message' => 'Period status updated successfully',
                'is_active' => $period->is_active
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating period status'
            ], 500);
        }
    }

    // Keep all existing methods
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'description' => 'required',
            'start_date' => 'required',
            'end_date' => 'required',
        ]);

        $qip = new Qip();
        $qip->name = $request->name;
        $qip->description = $request->description;
        $qip->start_date = $request->start_date;
        $qip->end_date = $request->end_date;
        $qip->save();

        return redirect()->route('qip.index');
    }

    public function getLgas($districtId)
    {
        $lgas = Lga::where('district_id', $districtId)->get(['id', 'name']);
        Log::info('LGA: ' . $lgas);
        return response()->json($lgas);
    }

    public function getPhcs($lgaId)
    {
        $phcs = Phc::where('lga_id', $lgaId)->get(['id', 'name']);
        Log::info('PHC: ' . $phcs);
        return response()->json($phcs);
    }

    private function calculateSafeCareLevel($compliancePercentage)
    {
        if ($compliancePercentage >= 90) {
            return "Level 5 - Excellent";
        } elseif ($compliancePercentage >= 80) {
            return "Level 4 - Very Good";
        } elseif ($compliancePercentage >= 70) {
            return "Level 3 - Good";
        } elseif ($compliancePercentage >= 60) {
            return "Level 2 - Fair";
        } elseif ($compliancePercentage >= 50) {
            return "Level 1 - Poor";
        } else {
            return "Below Level 1 - Critical";
        }
    }

    private function getPreviousResponses($assessment, $questions)
    {
        $responses = [];

        foreach ($questions as $question) {
            $columnName = 'q_' . str_replace('.', '_', $question->question_no);
            if (isset($assessment->{$columnName}) && !empty($assessment->{$columnName})) {
                $responses[$question->id] = [
                    'response' => $assessment->{$columnName},
                    'comment' => $assessment->{'comment_' . str_replace('.', '_', $question->question_no)} ?? null
                ];
            }
        }

        return $responses;
    }

    public function getAssessmentHistory(Request $request)
    {
        try {
            $phcId = $request->phc_id;
            $limit = $request->get('limit', 10);

            if (!$phcId) {
                return response()->json([
                    'success' => false,
                    'message' => 'PHC ID is required'
                ], 422);
            }

            $assessments = SafecareAssessment::where('phc_id', $phcId)
                ->with([
                    'user:id,full_name',
                    'phc:id,name',
                    'updatedBy:id,full_name',
                    'safecarePeriod:id,name,quarter,year'
                ])
                ->orderBy('assessment_date', 'desc')
                ->limit($limit)
                ->get();

            return response()->json([
                'success' => true,
                'assessments' => $assessments,
                'total' => $assessments->count()
            ]);
        } catch (\Exception $e) {
            Log::error('Error in getAssessmentHistory: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving assessment history'
            ], 500);
        }
    }

    public function compareAssessments(Request $request)
    {
        try {
            $phcIds = $request->phc_ids;
            $startDate = $request->start_date;
            $endDate = $request->end_date;
            $quarter = $request->quarter;
            $year = $request->year;

            if (!$phcIds || !is_array($phcIds)) {
                return response()->json([
                    'success' => false,
                    'message' => 'PHC IDs array is required'
                ], 422);
            }

            $query = SafecareAssessment::whereIn('phc_id', $phcIds)
                ->with(['phc', 'district', 'lga', 'safecarePeriod']);

            if ($quarter && $year) {
                $query->where('quarter', $quarter)->where('year', $year);
            } elseif ($startDate && $endDate) {
                $query->whereBetween('assessment_date', [$startDate, $endDate]);
            }

            $assessments = $query->get();

            $comparisonData = $assessments->groupBy('phc_id')->map(function ($phcAssessments) {
                return [
                    'phc' => $phcAssessments->first()->phc,
                    'assessments' => $phcAssessments,
                    'latest_assessment' => $phcAssessments->first(),
                    'average_compliance' => $phcAssessments->avg('compliance_percentage')
                ];
            });

            return response()->json([
                'success' => true,
                'comparison_data' => $comparisonData,
                'total_assessments' => $assessments->count()
            ]);
        } catch (\Exception $e) {
            Log::error('Error in compareAssessments: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error comparing assessments'
            ], 500);
        }
    }

    public function getAnalytics(Request $request)
    {
        try {
            $districtId = $request->district_id;
            $lgaId = $request->lga_id;
            $quarter = $request->quarter;
            $year = $request->year;
            $startDate = $request->start_date;
            $endDate = $request->end_date;

            $query = SafecareAssessment::query()->with(['phc', 'district', 'lga', 'safecarePeriod']);

            if ($districtId) {
                $query->where('district_id', $districtId);
            }

            if ($lgaId) {
                $query->where('lga_id', $lgaId);
            }

            if ($quarter && $year) {
                $query->where('quarter', $quarter)->where('year', $year);
            } elseif ($startDate && $endDate) {
                $query->whereBetween('assessment_date', [$startDate, $endDate]);
            }

            $assessments = $query->get();

            // Calculate analytics
            $analytics = [
                'total_assessments' => $assessments->count(),
                'average_compliance' => $assessments->avg('compliance_percentage'),
                'total_phcs_assessed' => $assessments->unique('phc_id')->count(),
                'compliance_distribution' => [
                    'excellent' => $assessments->where('compliance_percentage', '>=', 90)->count(),
                    'very_good' => $assessments->whereBetween('compliance_percentage', [80, 89])->count(),
                    'good' => $assessments->whereBetween('compliance_percentage', [70, 79])->count(),
                    'fair' => $assessments->whereBetween('compliance_percentage', [60, 69])->count(),
                    'poor' => $assessments->whereBetween('compliance_percentage', [50, 59])->count(),
                    'critical' => $assessments->where('compliance_percentage', '<', 50)->count(),
                ],
                'quarterly_trends' => $assessments->groupBy(function ($assessment) {
                    return $assessment->year . '-' . $assessment->quarter;
                })->map(function ($quarterAssessments) {
                    return [
                        'count' => $quarterAssessments->count(),
                        'average_compliance' => $quarterAssessments->avg('compliance_percentage'),
                        'quarter' => $quarterAssessments->first()->quarter,
                        'year' => $quarterAssessments->first()->year
                    ];
                }),
                'period_breakdown' => $assessments->groupBy('safecare_period_id')->map(function ($periodAssessments) {
                    $period = $periodAssessments->first()->safecarePeriod;
                    return [
                        'period_name' => $period ? $period->name : 'Unknown Period',
                        'count' => $periodAssessments->count(),
                        'average_compliance' => $periodAssessments->avg('compliance_percentage'),
                        'quarter' => $period ? $period->quarter : null,
                        'year' => $period ? $period->year : null
                    ];
                })
            ];

            return response()->json([
                'success' => true,
                'analytics' => $analytics
            ]);
        } catch (\Exception $e) {
            Log::error('Error in getAnalytics: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving analytics'
            ], 500);
        }
    }

    public function noAvailableAssessments()
    {
        $user = auth()->user();
        $now = now();

        // Get next scheduled period
        $nextPeriod = SafecarePeriod::where('start_date', '>', $now)
            ->where('is_active', true)
            ->orderBy('start_date')
            ->first();

        $nextAvailableDate = $nextPeriod ? $nextPeriod->start_date : null;
        $daysRemaining = $nextAvailableDate ? $now->diffInDays($nextAvailableDate) : null;

        return view('qip.no-available-assessments', [
            'nextAvailableDate' => $nextAvailableDate,
            'daysRemaining' => $daysRemaining,
            'info' => session('info', 'No SafeCare assessment period is currently active.')
        ]);
    }
}

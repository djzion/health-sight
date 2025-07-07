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
use Carbon\Carbon;

class QipController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $role = $user->role;
<<<<<<< HEAD
=======
        $now = now();
        $oneWeekAgo = $now->copy()->subWeek();
>>>>>>> a15ae561d52746b4fd377fd78effafc2d4fff0ee

        // Check if user is director/admin
        $isDirector = in_array($role->name, ['director', 'Director', 'admin', 'Admin']);

        $districts = District::all();
        $lgas = Lga::all();
        $phcs = Phc::all();

<<<<<<< HEAD
=======
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

>>>>>>> a15ae561d52746b4fd377fd78effafc2d4fff0ee
        return view('qip.index', compact(
            'districts',
            'lgas',
            'phcs',
<<<<<<< HEAD
            'isDirector'
        ));
    }

    public function getSafecareAssessment(Request $request)
    {
        try {
            $phcId = $request->phc_id;
            $lgaId = $request->lga_id;
            $districtId = $request->district_id;
            $quarter = $request->quarter;
            $year = $request->year;
            $assessmentDate = $request->assessment_date;
            $user = auth()->user();

            Log::info("Getting SafeCare assessment for PHC ID: {$phcId}, Quarter: {$quarter}, Year: {$year}");

            // Validate required parameters
            if (!$phcId || !$lgaId || !$districtId || !$quarter || !$year || !$assessmentDate) {
                return response()->json([
                    'error' => 'All parameters (PHC, LGA, District, Quarter, Year, and Assessment Date) are required.',
                    'can_access' => false
                ], 422);
            }

            // Validate assessment date - SIMPLIFIED: Only check if date is not in future
            try {
                $parsedDate = Carbon::parse($assessmentDate);
                if ($parsedDate->isFuture()) {
                    return response()->json([
                        'error' => 'Assessment date cannot be in the future.',
                        'can_access' => false
                    ], 422);
                }
            } catch (\Exception $e) {
                return response()->json([
                    'error' => 'Invalid assessment date format.',
                    'can_access' => false
                ], 422);
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

            // Check for existing assessment in the same quarter/year
            $existingAssessment = SafecareAssessment::where('phc_id', $phcId)
                ->where('user_id', $user->id)
                ->where('quarter', $quarter)
                ->where('year', $year)
                ->with(['user:id,full_name', 'updatedBy:id,full_name'])
                ->orderBy('assessment_date', 'desc')
                ->first();

            // Check edit permissions (7-day window)
            $canEdit = true;
            $editWindowExpired = false;
            $oneWeekAgo = now()->subWeek();

            if ($existingAssessment) {
                $canEdit = $existingAssessment->created_at->gt($oneWeekAgo);
                $editWindowExpired = !$canEdit;
            }

            Log::info("Assessment edit status", [
                'can_edit' => $canEdit,
                'edit_window_expired' => $editWindowExpired,
                'has_existing_assessment' => $existingAssessment !== null
            ]);

            // Build safecare_result data structure
            $safecareResult = null;
            if ($existingAssessment) {
                $safecareLevel = $this->calculateSafeCareLevel($existingAssessment->compliance_percentage);

                $safecareResult = [
                    'id' => $existingAssessment->id,
                    'safecare_level' => $safecareLevel,
                    'safecare_score' => $existingAssessment->compliance_percentage,
                    'compliance_percentage' => $existingAssessment->compliance_percentage,
                    'assessment_date' => $existingAssessment->assessment_date,
                    'created_at' => $existingAssessment->created_at,
                    'updated_at' => $existingAssessment->updated_at,
                    'last_updated_date' => $existingAssessment->last_updated_date,
                    'assessor_name' => $existingAssessment->user ? $existingAssessment->user->full_name : 'Unknown',
                    'user_name' => $existingAssessment->user ? $existingAssessment->user->full_name : 'Unknown',
                    'updated_by_name' => $existingAssessment->updatedBy ? $existingAssessment->updatedBy->full_name : null,
                    'total_questions' => $existingAssessment->total_questions,
                    'fully_compliant' => $existingAssessment->fully_compliant_count,
                    'partially_compliant' => $existingAssessment->partially_compliant_count,
                    'not_compliant' => $existingAssessment->not_compliant_count,
                    'not_applicable' => $existingAssessment->not_applicable_count,
                    'has_been_updated' => $existingAssessment->updated_by !== null,
                    'can_edit' => $canEdit,
                    'edit_window_expired' => $editWindowExpired,
                    'quarter' => $existingAssessment->quarter,
                    'year' => $existingAssessment->year
                ];
            }

            // Get previous responses for pre-populating form
            $previousResponses = null;
            if ($existingAssessment) {
                $previousResponses = $this->getPreviousResponses($existingAssessment, $questions);
            }

            $groupedQuestions = $questions->groupBy('section');

            return response()->json([
                'questions' => $questions,
                'groupedQuestions' => $groupedQuestions,
                'previousAssessment' => $previousResponses,
                'previousAssessmentDate' => $existingAssessment ? $existingAssessment->assessment_date->format('F j, Y') : null,
                'safecare_result' => $safecareResult,
                'has_previous_assessment' => $existingAssessment !== null,
                'can_edit' => $canEdit,
                'edit_window_expired' => $editWindowExpired,
                'selected_quarter' => $quarter,
                'selected_year' => $year,
                'selected_assessment_date' => $assessmentDate,
                'can_access' => true
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
            $oneWeekAgo = now()->subWeek();

            Log::info('saveSafecareAssessment called with params: ' . json_encode($request->all()));

            $data = $request->json()->all();
            if (empty($data)) {
                $data = $request->all();
            }

            $districtId = $data['district_id'] ?? $request->district_id;
            $lgaId = $data['lga_id'] ?? $request->lga_id;
            $phcId = $data['phc_id'] ?? $request->phc_id;
            $responses = $data['responses'] ?? $request->responses;
            $quarter = $data['quarter'] ?? $request->quarter;
            $year = $data['year'] ?? $request->year;
            $assessmentDate = $data['assessment_date'] ?? $request->assessment_date;

            // Validate required data
            if (!$districtId || !$lgaId || !$phcId || !$responses || !$quarter || !$year || !$assessmentDate) {
                return response()->json([
                    'success' => false,
                    'message' => 'Missing required data (District, LGA, PHC, Responses, Quarter, Year, Assessment Date)'
                ], 422);
            }

            // Validate assessment date - SIMPLIFIED: Only check if date is not in future
            try {
                $parsedAssessmentDate = Carbon::parse($assessmentDate);
                if ($parsedAssessmentDate->isFuture()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Assessment date cannot be in the future.'
                    ], 422);
                }
            } catch (\Exception $e) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid assessment date format.'
                ], 422);
            }

            // Check for existing assessment in the same quarter/year
            $existingAssessment = SafecareAssessment::where('user_id', $user->id)
                ->where('phc_id', $phcId)
                ->where('quarter', $quarter)
                ->where('year', $year)
                ->first();

            // Validate edit permissions for existing assessment
            if ($existingAssessment) {
                $canEdit = $existingAssessment->created_at->gt($oneWeekAgo);
                if (!$canEdit) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Assessment can no longer be edited. 7-day edit window has expired.'
                    ], 403);
                }

                // Update existing assessment
                return $this->updateExistingAssessment($existingAssessment, $responses, $parsedAssessmentDate, $quarter, $year);
            }

            // Start database transaction
            DB::beginTransaction();

            try {
                // Create new assessment
                $assessmentData = [
                    'user_id' => $user->id,
                    'district_id' => $districtId,
                    'lga_id' => $lgaId,
                    'phc_id' => $phcId,
                    'assessment_date' => $parsedAssessmentDate,
                    'total_questions' => count($responses),
                    'fully_compliant_count' => 0,
                    'partially_compliant_count' => 0,
                    'not_compliant_count' => 0,
                    'not_applicable_count' => 0,
                    'quarter' => $quarter,
                    'year' => $year,
                ];

                // Process responses and calculate compliance
                $this->processAssessmentResponses($assessmentData, $responses);

                Log::info('Assessment data prepared:', [
                    'total_questions' => $assessmentData['total_questions'],
                    'compliance_counts' => [
                        'fully_compliant' => $assessmentData['fully_compliant_count'],
                        'partially_compliant' => $assessmentData['partially_compliant_count'],
                        'not_compliant' => $assessmentData['not_compliant_count'],
                        'not_applicable' => $assessmentData['not_applicable_count'],
                    ],
                    'compliance_percentage' => $assessmentData['compliance_percentage'] ?? 'not calculated'
                ]);

                // Create assessment using proper mass assignment
                $assessment = SafecareAssessment::create($assessmentData);

                if (!$assessment) {
                    throw new \Exception('Failed to create assessment record');
                }

                // Load the user relationship
                $assessment->load('user:id,full_name');

                Log::info('Assessment saved successfully with ID: ' . $assessment->id);

                DB::commit();

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
                        'quarter' => $quarter,
                        'year' => $year
                    ]
                ]);
            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }
        } catch (\Exception $e) {
            Log::error('Error in saveSafecareAssessment: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());

            return response()->json([
                'success' => false,
                'message' => 'Server error: ' . $e->getMessage(),
                'debug_info' => [
                    'line' => $e->getLine(),
                    'file' => $e->getFile()
                ]
            ], 500);
        }
    }

    public function updateSafecareAssessment(Request $request)
    {
        try {
            DB::beginTransaction();

            Log::info('updateSafecareAssessment called with params: ' . json_encode($request->all()));

            $validatedData = $request->validate([
                'assessment_id' => 'required|exists:safecare_assessments,id',
                'district_id' => 'required|exists:districts,id',
                'lga_id' => 'required|exists:lgas,id',
                'phc_id' => 'required|exists:phcs,id',
                'responses' => 'required|array',
                'responses.*.question_id' => 'required|exists:safecare,id',
                'responses.*.response' => 'required|in:FC,PC,NC,NA',
                'quarter' => 'required|in:Q1,Q2,Q3,Q4',
                'year' => 'required|integer|min:2020|max:2030',
                'assessment_date' => 'required|date'
            ]);

            $assessmentId = $validatedData['assessment_id'];

            // Get the existing assessment with original assessor info
            $existingAssessment = SafecareAssessment::with(['user:id,full_name'])
                ->findOrFail($assessmentId);

            // Verify user can edit this assessment (7-day window)
            $oneWeekAgo = now()->subWeek();
            if (!$existingAssessment->created_at->gt($oneWeekAgo)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Assessment can no longer be edited. 7-day edit window has expired.'
                ], 403);
            }

            // Store original data for tracking
            $originalAssessmentDate = $existingAssessment->assessment_date;
            $originalCreatedAt = $existingAssessment->created_at;
            $originalAssessor = $existingAssessment->user;

            // Parse and validate assessment date - SIMPLIFIED: Only check if date is not in future
            $parsedAssessmentDate = Carbon::parse($validatedData['assessment_date']);
            if ($parsedAssessmentDate->isFuture()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Assessment date cannot be in the future.'
                ], 422);
            }

            // Prepare updated assessment data
            $updateData = [
                'total_questions' => count($validatedData['responses']),
                'fully_compliant_count' => 0,
                'partially_compliant_count' => 0,
                'not_compliant_count' => 0,
                'not_applicable_count' => 0,
                'updated_by' => auth()->id(),
                'last_updated_date' => now(),
                'quarter' => $validatedData['quarter'],
                'year' => $validatedData['year'],
                'assessment_date' => $parsedAssessmentDate,
            ];

            // Process responses and calculate compliance
            $this->processAssessmentResponses($updateData, $validatedData['responses']);

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
                    'assessment_date' => $existingAssessment->assessment_date,
                    'created_at' => $originalCreatedAt,
                    'updated_at' => $existingAssessment->updated_at,
                    'last_updated_date' => $existingAssessment->last_updated_date,
                    'original_assessor' => $originalAssessor ? ($originalAssessor->full_name ?? $originalAssessor->name) : 'Unknown',
                    'original_assessor_id' => $existingAssessment->user_id,
                    'updated_by' => $existingAssessment->updatedBy ? ($existingAssessment->updatedBy->full_name ?? $existingAssessment->updatedBy->name) : auth()->user()->full_name ?? auth()->user()->name,
                    'updated_by_id' => auth()->id(),
                    'is_updated' => true,
                    'has_been_updated' => true,
                    'quarter' => $validatedData['quarter'],
                    'year' => $validatedData['year']
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

    private function updateExistingAssessment($assessment, $responses, $assessmentDate, $quarter, $year)
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
                'assessment_date' => $assessmentDate,
                'quarter' => $quarter,
                'year' => $year,
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
                    'quarter' => $quarter,
                    'year' => $year
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

    // ... [Rest of the methods remain unchanged - processAssessmentResponses, getStandardColumnMapping,
    // diagnoseDatabaseIssues, store, getLgas, getPhcs, calculateSafeCareLevel, getPreviousResponses, etc.]

    private function processAssessmentResponses(&$assessmentData, $responses)
    {
        // Get question mapping
        $questions = Safecare::all()->keyBy('id');

        // Debug logging
        Log::info('Processing assessment responses', [
            'total_responses' => count($responses),
            'total_questions' => $questions->count()
        ]);

        // Create precise mapping based on question ID to handle all conflicts and duplicates
        $questionIdMapping = [];

        // Build complete ID-based mapping for all 131 questions
        foreach ($questions as $question) {
            $questionId = $question->id;
            $questionShort = $question->question_short;

            // Handle all specific question ID mappings based on diagnostic results
            switch ($questionId) {
                // Management of Information - Patient Record conflicts
                case 31: // "Patient record" - unique ID version
                    $questionIdMapping[$questionId] = 'Patient_record_unique_ID';
                    break;
                case 32: // "Patient record" - general version
                    $questionIdMapping[$questionId] = 'Patient_record';
                    break;

                // Infectious Waste Handling conflicts
                case 80: // Inpatient Care - infectious waste handling
                    $questionIdMapping[$questionId] = 'Guidelines_for_handling_infectious_waste';
                    break;
                case 96: // Laboratory - infectious waste handling
                    $questionIdMapping[$questionId] = 'Guidelines_for_handling_infectious_waste_lab';
                    break;

                // Medication Labelling conflicts
                case 109: // Dispensed medication labelling
                    $questionIdMapping[$questionId] = 'Medication_labelling_dispensed';
                    break;
                case 112: // General medication labelling
                    $questionIdMapping[$questionId] = 'Medication_labelling';
                    break;

                // Missing mappings from diagnostic
                case 79: // "Beds, matresses and linen"
                    $questionIdMapping[$questionId] = 'Beds_mattresses_and_linen';
                    break;
                case 116: // "Infrastucture inspections"
                    $questionIdMapping[$questionId] = 'Infrastructure_inspections';
                    break;

                // Standard mappings for all other questions
                default:
                    // Use the standard column mapping
                    $standardMapping = $this->getStandardColumnMapping();
                    if (isset($standardMapping[$questionShort])) {
                        $questionIdMapping[$questionId] = $standardMapping[$questionShort];
                    } else {
                        Log::warning('No standard mapping found for question', [
                            'question_id' => $questionId,
                            'question_short' => $questionShort,
                            'section' => $question->section,
                        ]);
                    }
                    break;
            }
        }

        Log::info('Question ID mapping created', [
            'total_mappings' => count($questionIdMapping),
            'conflict_resolutions' => [
                'question_31_patient_record_unique' => $questionIdMapping[31] ?? 'not found',
                'question_32_patient_record_general' => $questionIdMapping[32] ?? 'not found',
                'question_80_inpatient_waste' => $questionIdMapping[80] ?? 'not found',
                'question_96_lab_waste' => $questionIdMapping[96] ?? 'not found',
                'question_109_dispensed_labelling' => $questionIdMapping[109] ?? 'not found',
                'question_112_general_labelling' => $questionIdMapping[112] ?? 'not found',
                'question_79_beds_linen' => $questionIdMapping[79] ?? 'not found',
                'question_116_infrastructure' => $questionIdMapping[116] ?? 'not found',
            ]
        ]);

        // Clear existing question responses for updates
        foreach ($questionIdMapping as $questionId => $columnName) {
            $assessmentData[$columnName] = null;

            Log::info('Clearing column', [
                'question_id' => $questionId,
                'column_name' => $columnName,
            ]);
        }

        // Process each response using question ID mapping
        $mappedResponses = 0;
        $unmappedResponses = [];
        $conflictResolutions = [];

        foreach ($responses as $response) {
            if (!isset($response['question_id']) || !isset($response['response'])) {
                Log::warning('Invalid response structure', $response);
                continue;
            }

            $questionId = $response['question_id'];
            $responseValue = $response['response'];

            if (!isset($questions[$questionId])) {
                Log::warning('Question not found for ID: ' . $questionId);
                continue;
            }

            if (isset($questionIdMapping[$questionId])) {
                $columnName = $questionIdMapping[$questionId];
                $question = $questions[$questionId];

                // Track conflict resolutions
                if (in_array($questionId, [31, 32, 80, 96, 109, 112, 79, 116])) {
                    $conflictResolutions[] = [
                        'question_id' => $questionId,
                        'question_short' => $question->question_short,
                        'resolved_to_column' => $columnName,
                        'response' => $responseValue
                    ];
                }

                // Log the mapping for debugging
                Log::info('Mapping response', [
                    'question_id' => $questionId,
                    'original_question_short' => $question->question_short,
                    'mapped_column_name' => $columnName,
                    'response' => $responseValue,
                    'is_conflict_resolution' => in_array($questionId, [31, 32, 80, 96, 109, 112, 79, 116])
                ]);

                $assessmentData[$columnName] = $responseValue;
                $mappedResponses++;

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
            } else {
                $question = $questions[$questionId];
                $unmappedResponses[] = [
                    'question_id' => $questionId,
                    'question_short' => $question->question_short,
                    'section' => $question->section,
                    'response' => $responseValue
                ];

                Log::error('Cannot save response - no mapping found', [
                    'question_id' => $questionId,
                    'original_question_short' => $question->question_short,
                    'response' => $responseValue,
                    'section' => $question->section,
                ]);
            }
        }

        // Enhanced logging with conflict resolution details
        Log::info('Final assessment data', [
            'total_responses_received' => count($responses),
            'mapped_responses' => $mappedResponses,
            'unmapped_responses' => count($unmappedResponses),
            'conflict_resolutions_applied' => count($conflictResolutions),
            'compliance_counts' => [
                'fully_compliant' => $assessmentData['fully_compliant_count'],
                'partially_compliant' => $assessmentData['partially_compliant_count'],
                'not_compliant' => $assessmentData['not_compliant_count'],
                'not_applicable' => $assessmentData['not_applicable_count'],
            ],
            'conflict_details' => $conflictResolutions,
            'unmapped_details' => $unmappedResponses
        ]);

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

        Log::info('Compliance calculation', [
            'scoring_questions' => $scoringQuestions,
            'compliance_percentage' => $assessmentData['compliance_percentage'],
            'expected_total_responses' => 131,
            'actual_mapped_responses' => $mappedResponses
        ]);
    }

    // Helper method for standard column mapping
    private function getStandardColumnMapping()
    {
        return [
            // Governance and Management Section
            'Organogram' => 'Organogram',
            'Strategic plan, mission and operational plan' => 'Strategic_plan_mission_operational_plan',
            'Healthcare organization leader' => 'Healthcare_organization_leader',
            'Care and Services' => 'Care_and_Services',
            'Qualified supply chain manager' => 'Qualified_supply_chain_manager',
            'Organizational Q structure' => 'Organizational_Q_structure',
            'Books of accounts' => 'Books_of_accounts',
            'Cash management' => 'Cash_management',
            'Fixed asset register (FAR)' => 'Fixed_asset_register_FAR',
            'Business and management processes' => 'Business_and_management_processes',

            // Human Resources Management Section
            'Staffing plan' => 'Staffing_plan',
            'Performance review' => 'Performance_review',
            'Staff job descriptions' => 'Staff_job_descriptions',
            'Staff personnel files' => 'Staff_personnel_files',
            'Credentialling' => 'Credentialling',
            'Staff orientation' => 'Staff_orientation',
            'Staff education and training' => 'Staff_education_and_training',

            // Patient and Family Rights and Access to Care Section
            'Patient rights' => 'Patient_rights',
            'Patient\'s privacy' => 'Patients_privacy',
            'Patient and family health education' => 'Patient_and_family_health_education',
            'Patient information' => 'Patient_information',
            'Informed consent' => 'Informed_consent',
            'Complaints process' => 'Complaints_process',
            'Opening hours display' => 'Opening_hours_display',
            'Signage' => 'Signage',
            'Information about services and related fees' => 'Information_about_services_and_related_fees',

            // Management of Information Section
            'HMIS' => 'HMIS',
            'Internal data analysis meetings' => 'Internal_data_analysis_meetings',
            'Health record checks' => 'Health_record_checks',
            'Health record storage' => 'Health_record_storage',

            // Risk Management Section
            'Qualified risk manager' => 'Qualified_risk_manager',
            'Risk management plan' => 'Risk_management_plan',
            'Occupational Health Safety (OHS)' => 'Occupational_Health_Safety_OHS',
            'Security sytem' => 'Security_system',
            'Fire fighting equipment' => 'Fire_fighting_equipment',
            'IPC policies' => 'IPC_policies',
            'Healthcare waste collection assets' => 'Healthcare_waste_collection_assets',

            // Primary Healthcare (Outpatient) Services Section
            'Number of staff' => 'Number_of_staff',
            'OPD lay-out' => 'OPD_layout',
            'Waiting area ventilation and cleanliness' => 'Waiting_area_ventilation_and_cleanliness',
            'Sufficient consultation rooms' => 'Sufficient_consultation_rooms',
            'Handwashing facilities' => 'Handwashing_facilities',
            'PPE' => 'PPE',
            'Sterilization equipment' => 'Sterilization_equipment',
            'Processing sterile packs' => 'Processing_sterile_packs',
            'Triage process' => 'Triage_process',
            'Qualified staff for conducting assessments' => 'Qualified_staff_for_conducting_assessments',
            'Guideline and knowledge about Sexual Transmitted Infections (STI)' => 'Guideline_knowledge_Sexual_Transmitted_Infections_STI',
            'Guideline and knowledge about Rapid Diagnostic Tests (RDT)' => 'Guideline_knowledge_Rapid_Diagnostic_Tests_RDT',
            'Malaria diagnostics' => 'Malaria_diagnostics',
            'Minor surgery equipment' => 'Minor_surgery_equipment',
            'Vital signs equipment' => 'Vital_signs_equipment',
            'Resuscitation training' => 'Resuscitation_training',
            'Emergency guidelines' => 'Emergency_guidelines',
            'Emergency tray or trolley' => 'Emergency_tray_or_trolley',
            'Oxygen supplies' => 'Oxygen_supplies',
            'Referral organzizations list' => 'Referral_organizations_list',
            'Ambulance' => 'Ambulance',
            'Contraceptive methods' => 'Contraceptive_methods',
            'ANC guideline and checklist' => 'ANC_guideline_and_checklist',
            'Delivery room and delivery bed' => 'Delivery_room_and_delivery_bed',
            'Partograph' => 'Partograph',
            'Neonatal resuscitation equipment' => 'Neonatal_resuscitation_equipment',
            'Postnatal guidelines' => 'Postnatal_guidelines',
            'Immunization (vacciation cards)' => 'Immunization_vaccination_cards',
            'Child growth monitoring' => 'Child_growth_monitoring',
            'Health education (ORS)' => 'Health_education_ORS',
            'TB treatment guidelines' => 'TB_treatment_guidelines',
            'VCT/PITC materials' => 'VCT_PITC_materials',
            'Guidelines for ART' => 'Guidelines_for_ART',
            'Qualified specialized staff' => 'Qualified_specialized_staff',
            'Guidelines for cleaning and disfection' => 'Guidelines_for_cleaning_and_disinfection',

            // Inpatient Care Section
            'Duty rosters' => 'Duty_rosters',
            'Ward rounds and documentation' => 'Ward_rounds_and_documentation',
            'Identification of patients' => 'Identification_of_patients',
            'Adequate space and privacy' => 'Adequate_space_and_privacy',
            'Sufficient and operational handwashing stations' => 'Sufficient_operational_handwashing_stations',
            'Vital signs monitoring' => 'Vital_signs_monitoring',
            'Management of pain' => 'Management_of_pain',
            'Protocol compliance' => 'Protocol_compliance',
            'Resuscitation equipment' => 'Resuscitation_equipment',
            'Guidelines for administering oxygen' => 'Guidelines_for_administering_oxygen',
            'Patient identification' => 'Patient_identification',
            'Guideline compliance' => 'Guideline_compliance',
            'Patient and family education' => 'Patient_and_family_education',
            'Mobility devices' => 'Mobility_devices',
            'Discharge instructions' => 'Discharge_instructions',
            'Policy for deceased patients' => 'Policy_for_deceased_patients',

            // Laboratory Services Section
            'Qualified laboratory manager' => 'Qualified_laboratory_manager',
            'Sufficient laboratory staff' => 'Sufficient_laboratory_staff',
            'Laboratory design' => 'Laboratory_design',
            'Sufficient and adequeate Personal Protective Equipment (PPE)' => 'Sufficient_adequate_Personal_Protective_Equipment_PPE',
            'Supplies for specimen collection' => 'Supplies_for_specimen_collection',
            'Labelling of specimens' => 'Labelling_of_specimens',
            'Assay SOPs' => 'Assay_SOPs',
            'Sufficient laboratory equipment' => 'Sufficient_laboratory_equipment',
            'Storage and labelling of reagents' => 'Storage_and_labelling_of_reagents',
            'Internal Quality Controls (IQA)' => 'Internal_Quality_Controls_IQA',
            'Result registration' => 'Result_registration',
            'Referral register' => 'Referral_register',

            // Diagnostic Imaging Service Section
            'Request forms' => 'Request_forms',

            // Medication Management Section
            'Qualified pharmacy manager' => 'Qualified_pharmacy_manager',
            'Availability of medication' => 'Availability_of_medication',
            'Guidelines for procurement of medication' => 'Guidelines_for_procurement_of_medication',
            'Storage area safety' => 'Storage_area_safety',
            'Prescription requirements' => 'Prescription_requirements',
            'Dispensing area' => 'Dispensing_area',
            'Medication error reporting' => 'Medication_error_reporting',

            // Facility Management Services Section
            'Infrastructure healthcare organization' => 'Infrastructure_healthcare_organization',
            'Maintenance (back-up) services' => 'Maintenance_backup_services',
            'Electrical power' => 'Electrical_power',
            'Water supply' => 'Water_supply',
            'Sewerage system' => 'Sewerage_system',
            'Quality of toilets and washrooms' => 'Quality_of_toilets_and_washrooms',
            'Equipment maintenance' => 'Equipment_maintenance',
            'Medical gas and supplies' => 'Medical_gas_and_supplies',
            'Vacuum suction equipment' => 'Vacuum_suction_equipment',
            'ICT equipment' => 'ICT_equipment',

            // Support Services Section
            'Laundry staff orientation' => 'Laundry_staff_orientation',
            'Laundry area' => 'Laundry_area',
            'Awareness of infection prevention and safety' => 'Awareness_of_infection_prevention_and_safety',
            'Cleaning materials' => 'Cleaning_materials',
            'Waste management' => 'Waste_management'
        ];
    }

    public function diagnoseDatabaseIssues()
    {
        try {
            // Get the latest assessment
            $latestAssessment = SafecareAssessment::latest()->first();

            if (!$latestAssessment) {
                return response()->json(['error' => 'No assessments found']);
            }

            // Get all questions
            $questions = Safecare::where('status', 'active')->get();

            // Get fillable columns (question columns only)
            $fillableColumns = (new SafecareAssessment)->getFillable();
            $excludeColumns = [
                'id',
                'user_id',
                'updated_by',
                'district_id',
                'lga_id',
                'phc_id',
                'assessment_date',
                'last_updated_date',
                'total_questions',
                'fully_compliant_count',
                'partially_compliant_count',
                'not_compliant_count',
                'not_applicable_count',
                'compliance_percentage',
                'safecare_period_id',
                'quarter',
                'year',
                'created_at',
                'updated_at'
            ];

            $questionColumns = array_filter($fillableColumns, function ($column) use ($excludeColumns) {
                return !in_array($column, $excludeColumns) && !str_starts_with($column, 'comment_');
            });

            // Analyze the latest assessment
            $assessmentData = $latestAssessment->toArray();
            $nullColumns = [];
            $filledColumns = [];

            foreach ($questionColumns as $column) {
                if (is_null($assessmentData[$column]) || $assessmentData[$column] === '') {
                    $nullColumns[] = $column;
                } else {
                    $filledColumns[$column] = $assessmentData[$column];
                }
            }

            // Find questions that might not have corresponding columns
            $questionWithoutColumns = [];
            $columnsWithoutQuestions = $questionColumns;

            foreach ($questions as $question) {
                $found = false;
                foreach ($questionColumns as $column) {
                    // Simple matching logic
                    $normalizedQuestion = str_replace([' ', ',', '&', '(', ')', '-', '.', '/', '\''], '', strtolower($question->question_short));
                    $normalizedColumn = str_replace(['_'], '', strtolower($column));

                    if (str_contains($normalizedColumn, substr($normalizedQuestion, 0, 10))) {
                        $found = true;
                        $columnsWithoutQuestions = array_diff($columnsWithoutQuestions, [$column]);
                        break;
                    }
                }

                if (!$found) {
                    $questionWithoutColumns[] = [
                        'id' => $question->id,
                        'question_short' => $question->question_short,
                        'section' => $question->section
                    ];
                }
            }

            // Check for duplicate question_short values
            $duplicateQuestions = [];
            $questionShorts = $questions->pluck('question_short')->toArray();
            $valueCounts = array_count_values($questionShorts);

            foreach ($valueCounts as $questionShort => $count) {
                if ($count > 1) {
                    $duplicateQuestions[$questionShort] = $questions->where('question_short', $questionShort)->values();
                }
            }

            return response()->json([
                'assessment_id' => $latestAssessment->id,
                'total_question_columns' => count($questionColumns),
                'null_columns_count' => count($nullColumns),
                'filled_columns_count' => count($filledColumns),
                'null_columns' => $nullColumns,
                'filled_columns_sample' => array_slice($filledColumns, 0, 10, true),
                'questions_without_columns' => $questionWithoutColumns,
                'columns_without_questions' => array_values($columnsWithoutQuestions),
                'duplicate_questions' => $duplicateQuestions,
                'compliance_summary' => [
                    'fully_compliant' => $latestAssessment->fully_compliant_count,
                    'partially_compliant' => $latestAssessment->partially_compliant_count,
                    'not_compliant' => $latestAssessment->not_compliant_count,
                    'not_applicable' => $latestAssessment->not_applicable_count,
                    'total_questions' => $latestAssessment->total_questions,
                    'compliance_percentage' => $latestAssessment->compliance_percentage
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
=======
            'canAccessAssessment',
            'nextAvailableDate',
            'activePeriod',
            'canEdit',
            'editWindowExpired',
            'newAssessmentPeriod',
            'isDirector'
        ));
>>>>>>> a15ae561d52746b4fd377fd78effafc2d4fff0ee
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
<<<<<<< HEAD
        }
    }

    private function getPreviousResponses($assessment, $questions)
    {
        $responses = [];
        $fillableColumns = (new SafecareAssessment)->getFillable();

        Log::info('Getting previous responses for assessment ID: ' . $assessment->id);

        // Create a mapping function to convert question_short to database column names
        $getColumnName = function ($questionShort) {
            return str_replace([' ', ',', '&', '(', ')', '-', '.'], '_', $questionShort);
        };

        foreach ($questions as $question) {
            $originalColumnName = $question->question_short;
            $convertedColumnName = $getColumnName($originalColumnName);

            // Use the converted column name if it exists in fillable, otherwise use original
            $columnName = in_array($convertedColumnName, $fillableColumns) ? $convertedColumnName : $originalColumnName;

            if (isset($assessment->{$columnName}) && !empty($assessment->{$columnName})) {
                $responses[$question->id] = [
                    'response' => $assessment->{$columnName},
                    // Add comment support if you have comment columns
                    // 'comment' => $assessment->{'comment_' . $question->question_short} ?? null
                ];

                Log::info('Found previous response', [
                    'question_id' => $question->id,
                    'original_question_short' => $originalColumnName,
                    'column_used' => $columnName,
                    'response' => $assessment->{$columnName}
                ]);
            } else {
                Log::info('No previous response found', [
                    'question_id' => $question->id,
                    'original_question_short' => $originalColumnName,
                    'column_tried' => $columnName,
                    'column_exists' => isset($assessment->{$columnName}),
                    'column_value' => $assessment->{$columnName} ?? 'NULL'
                ]);
            }
        }

        Log::info('Total previous responses found: ' . count($responses));

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
                    'updatedBy:id,full_name'
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
                ->with(['phc', 'district', 'lga']);

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

    public function debugQuestionMapping()
    {
        try {
            // Get first 5 questions for debugging
            $questions = Safecare::where('status', 'active')->limit(5)->get();

            $debug_info = [];

            foreach ($questions as $question) {
                $debug_info[] = [
                    'id' => $question->id,
                    'question_no' => $question->question_no,
                    'question_short' => $question->question_short,
                    'section' => $question->section,
                    'column_exists_in_fillable' => in_array($question->question_short, (new SafecareAssessment)->getFillable())
                ];
            }

            // Also get sample assessment data
            $assessment = SafecareAssessment::latest()->first();
            $assessment_data = $assessment ? $assessment->toArray() : null;

            return response()->json([
                'questions_debug' => $debug_info,
                'latest_assessment_columns' => $assessment_data ? array_keys($assessment_data) : [],
                'fillable_columns' => (new SafecareAssessment)->getFillable(),
                'total_questions' => Safecare::where('status', 'active')->count(),
                'total_assessments' => SafecareAssessment::count()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }

    public function generateColumnMapping()
    {
        try {
            // Get all questions from safecare table
            $questions = Safecare::where('status', 'active')->get();

            // Get fillable columns from the model
            $fillableColumns = (new SafecareAssessment)->getFillable();

            // Get actual database columns (exclude non-question columns)
            $excludeColumns = [
                'id',
                'user_id',
                'updated_by',
                'district_id',
                'lga_id',
                'phc_id',
                'assessment_date',
                'last_updated_date',
                'total_questions',
                'fully_compliant_count',
                'partially_compliant_count',
                'not_compliant_count',
                'not_applicable_count',
                'compliance_percentage',
                'safecare_period_id',
                'quarter',
                'year',
                'created_at',
                'updated_at'
            ];

            $questionColumns = array_filter($fillableColumns, function ($column) use ($excludeColumns) {
                return !in_array($column, $excludeColumns) && !str_starts_with($column, 'comment_');
            });

            $mapping = [];
            $unmapped = [];

            foreach ($questions as $question) {
                $questionShort = $question->question_short;

                // Try to find exact match first
                if (in_array($questionShort, $questionColumns)) {
                    $mapping[$questionShort] = $questionShort;
                    continue;
                }

                // Try to find similar column
                $found = false;
                foreach ($questionColumns as $column) {
                    // Check if they match when normalized
                    $normalizedQuestion = str_replace([' ', ',', '&', '(', ')', '-', '.', '/'], '_', $questionShort);
                    $normalizedColumn = str_replace([' ', ',', '&', '(', ')', '-', '.', '/'], '_', $column);

                    if (strtolower($normalizedQuestion) === strtolower($normalizedColumn)) {
                        $mapping[$questionShort] = $column;
                        $found = true;
                        break;
                    }

                    // Check partial matches
                    if (str_contains(strtolower($column), strtolower(str_replace([' ', '_'], '', $questionShort)))) {
                        $mapping[$questionShort] = $column;
                        $found = true;
                        break;
                    }
                }

                if (!$found) {
                    $unmapped[] = [
                        'id' => $question->id,
                        'question_no' => $question->question_no,
                        'question_short' => $questionShort,
                        'section' => $question->section
                    ];
                }
            }

            return response()->json([
                'total_questions' => $questions->count(),
                'total_columns' => count($questionColumns),
                'mapped_count' => count($mapping),
                'unmapped_count' => count($unmapped),
                'mapping' => $mapping,
                'unmapped_questions' => $unmapped,
                'all_question_columns' => $questionColumns,
                'sample_questions' => $questions->take(5)->pluck('question_short', 'id')->toArray()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
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

            $query = SafecareAssessment::query()->with(['phc', 'district', 'lga']);

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
=======
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
>>>>>>> a15ae561d52746b4fd377fd78effafc2d4fff0ee
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

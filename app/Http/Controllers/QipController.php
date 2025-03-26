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
use App\Models\SafecareResponses;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class QipController extends Controller
{
    public function index()
    {
        $districts = District::all();
        $lgas = Lga::all();
        $phcs = Phc::all();

        return view('qip.index', compact('districts', 'lgas', 'phcs'));
    }

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

    // public function getSafecareAssessment(Request $request)
    // {
    //     try {
    //         $questions = Safecare::where('status', 'active')
    //             ->select('id', 'question_no', 'question_description', 'question_short', 'section', 'status')
    //             ->orderBy('section')
    //             ->orderBy('question_no')
    //             ->get();

    //         Log::info('Questions count: ' . $questions->count());

    //         if ($questions->isEmpty()) {
    //             Log::warning('No active questions found in the safecare table');
    //             return response()->json([
    //                 'questions' => [],
    //                 'groupedQuestions' => [],
    //                 'error' => 'No active questions found'
    //             ]);
    //         }

    //         $groupedQuestions = $questions->groupBy('section');

    //         return response()->json([
    //             'questions' => $questions,
    //             'groupedQuestions' => $groupedQuestions
    //         ]);
    //     } catch (\Exception $e) {
    //         Log::error('Error in getSafecareAssessment: ' . $e->getMessage());
    //         return response()->json([
    //             'error' => 'An error occurred while fetching assessment questions'
    //         ], 500);
    //     }
    // }

    public function getSafecareAssessment(Request $request)
    {
        try {
            // Get the selected PHC, LGA, and District IDs
            $phcId = $request->phc_id;
            $lgaId = $request->lga_id;
            $districtId = $request->district_id;

            // Get the SafeCare questions
            $questions = Safecare::where('status', 'active')
                ->select('id', 'question_no', 'question_description', 'question_short', 'section', 'status')
                ->orderBy('section')
                ->orderBy('question_no')
                ->get();

            Log::info('Questions count: ' . $questions->count());

            if ($questions->isEmpty()) {
                Log::warning('No active questions found in the safecare table');
                return response()->json([
                    'questions' => [],
                    'groupedQuestions' => [],
                    'error' => 'No active questions found'
                ]);
            }

            // Get previous assessment data from safecare_results
            $previousResult = DB::table('safecare_results')
            ->where('safecare_results.phc_id', $phcId)  // Specify the table name to remove ambiguity
            ->join('users', 'safecare_results.user_id', '=', 'users.id')
            ->select(
                'safecare_results.*',
                'users.full_name as assessor_name'
            )
            ->orderBy('safecare_results.last_assessment', 'desc')
            ->first();

            // Get previous responses
            $previousAssessment = null;
            $previousAssessmentDate = null;

            if ($phcId) {
                $questionIds = $questions->pluck('id')->toArray();

                $previousResponses = SafecareResponses::where('phc_id', $phcId)
                    ->whereIn('safecare_id', $questionIds)
                    ->select('safecare_id', 'response', 'comment', 'created_at')
                    ->orderBy('created_at', 'desc')
                    ->get()
                    ->groupBy('safecare_id');

                if ($previousResponses->count() > 0) {
                    $latestDate = SafecareResponses::where('phc_id', $phcId)
                        ->whereIn('safecare_id', $questionIds)
                        ->max('created_at');

                    $previousAssessment = SafecareResponses::where('phc_id', $phcId)
                        ->whereIn('safecare_id', $questionIds)
                        ->whereDate('created_at', date('Y-m-d', strtotime($latestDate)))
                        ->select('safecare_id', 'response', 'comment', 'created_at')
                        ->get()
                        ->keyBy('safecare_id');

                    $previousAssessmentDate = date('F j, Y', strtotime($latestDate));
                }
            }

            $groupedQuestions = $questions->groupBy('section');

            return response()->json([
                'questions' => $questions,
                'groupedQuestions' => $groupedQuestions,
                'previousAssessment' => $previousAssessment,
                'previousAssessmentDate' => $previousAssessmentDate ?? null,
                'safecare_result' => $previousResult
            ]);
        } catch (\Exception $e) {
            Log::error('Error in getSafecareAssessment: ' . $e->getMessage());
            return response()->json([
                'error' => 'An error occurred while fetching assessment questions'
            ], 500);
        }
    }

    public function saveSafecareAssessment(Request $request)
    {
        try {
            // Log the incoming request for debugging
            Log::info('saveSafecareAssessment called with params: ' . json_encode($request->all()));

            // Get JSON body if content-type is application/json
            $data = $request->json()->all();
            if (empty($data)) {
                $data = $request->all();
            }

            // Extract data
            $districtId = $data['district_id'] ?? $request->district_id;
            $lgaId = $data['lga_id'] ?? $request->lga_id;
            $phcId = $data['phc_id'] ?? $request->phc_id;
            $responses = $data['responses'] ?? $request->responses;

            // Validate essential data
            if (!$districtId || !$lgaId || !$phcId || !$responses) {
                return response()->json([
                    'success' => false,
                    'message' => 'Missing required data'
                ], 422);
            }

            // Save each response
            foreach ($responses as $response) {
                // Check for required fields
                if (!isset($response['question_id']) || !isset($response['response'])) {
                    continue; // Skip invalid responses
                }

                // Create the response record
                SafecareResponses::create([
                    'user_id' => Auth::id(),
                    'district_id' => $districtId,
                    'lga_id' => $lgaId,
                    'phc_id' => $phcId,
                    'safecare_id' => $response['question_id'], // Match your DB column name
                    'response' => $response['response'],
                    // Add comment if available
                    'comment' => $response['comment'] ?? null
                ]);
            }

            // Return success
            return response()->json([
                'success' => true,
                'message' => 'SafeCare Assessment successfully completed',
                'count' => count($responses)
            ]);
        } catch (\Exception $e) {
            // Log error
            Log::error('Error in saveSafecareAssessment: ' . $e->getMessage());
            Log::error($e->getTraceAsString());

            // Return error response
            return response()->json([
                'success' => false,
                'message' => 'Server error: ' . $e->getMessage()
            ], 500);
        }
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Assessment;
use App\Models\AssessmentResponse;
use Illuminate\Http\Request;

class UserDashboardController extends Controller
{
    public function dashboard()
    {
        $user = auth()->user();
        $userRoles = $user->roles;
        $assessmentCategories = $userRoles->load('assessmentCategories')->assessmentCategories;
        $assessments = Assessment::whereIn('assessment_category_id', $assessmentCategories->pluck('id'))->get();

        return view('dashboard', compact('assessments'));
    }

    public function submitAssessment(Request $request)
    {
        $validatedData = $request->validate([
            'responses.*' => 'required|array',
            'responses.*.*' => 'required|string|max:500',
        ]);

        foreach ($validatedData['responses'] as $assessmentId => $response) {
            // Save the user's responses
            AssessmentResponse::create([
                'user_id' => auth()->id(),
                'assessment_id' => $assessmentId,
                'response' => json_encode($response),
            ]);
        }

        return redirect()->back()->with('success', 'Responses submitted successfully.');
    }
}

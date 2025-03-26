<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Assessment;
use Illuminate\Http\Request;

class AdminAssessmentController extends Controller
{
    /**
     * Set the next assessment date
     */
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
}

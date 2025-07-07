<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class AssessmentResponse extends Model
{
    protected $fillable = [
        'user_id',
        'district_id',
        'lga_id',
        'phc_id',
        'assessment_id',
        'response',
        'assessment_period_id', // Keep for backward compatibility if needed
        'quarter', // User-selected quarter (Q1, Q2, Q3, Q4)
        'assessment_date', // The actual date when assessment is conducted (flexible)
        'year', // User-selected year
        'additional_response',
        'comments',
        'submitted_at', // When the form was submitted to the system
        'is_final_submission'
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
        'assessment_date' => 'date', // Cast to date for proper handling
        'is_final_submission' => 'boolean',
        'year' => 'integer'
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function assessment()
    {
        return $this->belongsTo(Assessment::class);
    }

    public function district()
    {
        return $this->belongsTo(District::class);
    }

    public function lga()
    {
        return $this->belongsTo(Lga::class);
    }

    public function phc()
    {
        return $this->belongsTo(Phc::class);
    }

    public function assessmentPeriod()
    {
        return $this->belongsTo(AssessmentPeriod::class);
    }

    // Scopes for querying by period
    public function scopeForPeriod($query, $quarter, $year)
    {
        return $query->where('quarter', $quarter)->where('year', $year);
    }

    public function scopeForCurrentQuarter($query)
    {
        $currentQuarter = $this->getCurrentQuarter();
        $currentYear = date('Y');

        return $query->forPeriod($currentQuarter, $currentYear);
    }

    public function scopeForUserAndPHC($query, $userId, $phcId)
    {
        return $query->where('user_id', $userId)->where('phc_id', $phcId);
    }

    public function scopeForLocation($query, $districtId, $lgaId, $phcId)
    {
        return $query->where('district_id', $districtId)
                    ->where('lga_id', $lgaId)
                    ->where('phc_id', $phcId);
    }

    // Scope for assessment date range
    public function scopeAssessedBetween($query, $startDate, $endDate)
    {
        return $query->whereBetween('assessment_date', [$startDate, $endDate]);
    }

    public function scopeAssessedInMonth($query, $year, $month)
    {
        return $query->whereYear('assessment_date', $year)
                    ->whereMonth('assessment_date', $month);
    }

    // Helper methods
    public function getFormattedResponse()
    {
        $user = auth('web')->user();

        if ($this->user_id != $user->id) {
            return null;
        }

        $assessment = $this->assessment;

        if ($assessment && $assessment->response_type === 'conditional_text') {
            return [
                'main' => $this->attributes['response'],
                'additional' => $this->comments ?? null
            ];
        }

        return $this->attributes['response'];
    }

    public function canBeEdited()
    {
        if ($this->is_final_submission && $this->submitted_at) {
            return $this->submitted_at->greaterThan(now()->subWeek());
        }

        return $this->created_at->greaterThan(now()->subWeek());
    }

    public function getEditExpirationDate()
    {
        if ($this->is_final_submission && $this->submitted_at) {
            return $this->submitted_at->addWeek();
        }

        return $this->created_at->addWeek();
    }

    public function getDecodedResponseAttribute()
    {
        if (is_string($this->response) && $this->isJson($this->response)) {
            return json_decode($this->response, true);
        }

        return $this->response;
    }

    public function getQuarterDisplayAttribute()
    {
        $quarterMap = [
            'Q1' => 'Q1 (Jan-Mar)',
            'Q2' => 'Q2 (Apr-Jun)',
            'Q3' => 'Q3 (Jul-Sep)',
            'Q4' => 'Q4 (Oct-Dec)'
        ];

        return $quarterMap[$this->quarter] ?? $this->quarter;
    }

    public function getPeriodDisplayAttribute()
    {
        return $this->quarter_display . ' ' . $this->year;
    }

    public function getFormattedSubmissionDateAttribute()
    {
        if ($this->submitted_at) {
            return $this->submitted_at->format('M j, Y');
        }

        return $this->created_at->format('M j, Y');
    }

    public function getFormattedAssessmentDateAttribute()
    {
        if ($this->assessment_date) {
            return $this->assessment_date->format('M j, Y');
        }

        return 'Not specified';
    }

    public function getAssessmentTimingAttribute()
    {
        if (!$this->assessment_date) {
            return 'Unknown timing';
        }

        $assessmentDate = Carbon::parse($this->assessment_date);
        $quarter = $this->quarter;
        $year = $this->year;

        // Define quarter months
        $quarterMonths = [
            'Q1' => ['start' => 1, 'end' => 3], // Jan-Mar
            'Q2' => ['start' => 4, 'end' => 6], // Apr-Jun
            'Q3' => ['start' => 7, 'end' => 9], // Jul-Sep
            'Q4' => ['start' => 10, 'end' => 12] // Oct-Dec
        ];

        if (!isset($quarterMonths[$quarter])) {
            return 'Unknown timing';
        }

        $quarterStart = Carbon::create($year, $quarterMonths[$quarter]['start'], 1);
        $quarterEnd = Carbon::create($year, $quarterMonths[$quarter]['end'], 1)->endOfMonth();

        if ($assessmentDate->between($quarterStart, $quarterEnd)) {
            return 'On time';
        } elseif ($assessmentDate->gt($quarterEnd)) {
            $monthsLate = $assessmentDate->diffInMonths($quarterEnd);
            return $monthsLate === 1 ? '1 month late' : "{$monthsLate} months late";
        } else {
            return 'Early assessment';
        }
    }

    public function isLateAssessment()
    {
        if (!$this->assessment_date) {
            return false;
        }

        $assessmentDate = Carbon::parse($this->assessment_date);
        $quarter = $this->quarter;
        $year = $this->year;

        // Define quarter end dates
        $quarterEndMonths = [
            'Q1' => 3, // March
            'Q2' => 6, // June
            'Q3' => 9, // September
            'Q4' => 12 // December
        ];

        if (!isset($quarterEndMonths[$quarter])) {
            return false;
        }

        $quarterEnd = Carbon::create($year, $quarterEndMonths[$quarter], 1)->endOfMonth();

        return $assessmentDate->gt($quarterEnd);
    }

    // Static helper methods
    public static function getCurrentQuarter()
    {
        $month = date('n');
        if ($month <= 3) return 'Q1';
        if ($month <= 6) return 'Q2';
        if ($month <= 9) return 'Q3';
        return 'Q4';
    }

    public static function getQuarterOptions()
    {
        return [
            'Q1' => 'Q1 (Jan-Mar)',
            'Q2' => 'Q2 (Apr-Jun)',
            'Q3' => 'Q3 (Jul-Sep)',
            'Q4' => 'Q4 (Oct-Dec)'
        ];
    }

    public static function getYearOptions($yearsBack = 5)
    {
        $currentYear = date('Y');
        $years = [];

        for ($i = 0; $i <= $yearsBack; $i++) {
            $year = $currentYear - $i;
            $years[$year] = $year;
        }

        return $years;
    }

    // Check if response exists for specific criteria
    public static function existsForPeriod($userId, $phcId, $quarter, $year, $assessmentId = null)
    {
        $query = static::where('user_id', $userId)
            ->where('phc_id', $phcId)
            ->where('quarter', $quarter)
            ->where('year', $year);

        if ($assessmentId) {
            $query->where('assessment_id', $assessmentId);
        }

        return $query->exists();
    }

    // Get all responses for a specific period
    public static function getResponsesForPeriod($userId, $phcId, $quarter, $year)
    {
        return static::where('user_id', $userId)
            ->where('phc_id', $phcId)
            ->where('quarter', $quarter)
            ->where('year', $year)
            ->with(['assessment', 'assessment.section'])
            ->get();
    }

    // Enhanced method to get responses with location validation
    public static function getResponsesForPeriodAndLocation($userId, $districtId, $lgaId, $phcId, $quarter, $year)
    {
        return static::where('user_id', $userId)
            ->where('district_id', $districtId)
            ->where('lga_id', $lgaId)
            ->where('phc_id', $phcId)
            ->where('quarter', $quarter)
            ->where('year', $year)
            ->with(['assessment', 'assessment.section', 'district', 'lga', 'phc'])
            ->get();
    }

    // Get late assessments
    public static function getLateAssessments($userId = null, $phcId = null)
    {
        $query = static::whereNotNull('assessment_date');

        if ($userId) {
            $query->where('user_id', $userId);
        }

        if ($phcId) {
            $query->where('phc_id', $phcId);
        }

        return $query->get()->filter(function ($response) {
            return $response->isLateAssessment();
        });
    }

    // Validation helpers
    public function isValidQuarter($quarter)
    {
        return in_array($quarter, ['Q1', 'Q2', 'Q3', 'Q4']);
    }

    public function isValidYear($year)
    {
        $currentYear = date('Y');
        return $year >= 2020 && $year <= $currentYear;
    }

    public function isValidAssessmentDate($date)
    {
        $assessmentDate = Carbon::parse($date);
        $today = Carbon::today();
        $twoYearsAgo = $today->copy()->subYears(2);

        // Assessment date should not be in the future and not older than 2 years
        return $assessmentDate->lte($today) && $assessmentDate->gte($twoYearsAgo);
    }

    // Private helper methods
    private function isJson($string)
    {
        json_decode($string);
        return json_last_error() === JSON_ERROR_NONE;
    }

    // Mutators
    public function setQuarterAttribute($value)
    {
        if (!$this->isValidQuarter($value)) {
            throw new \InvalidArgumentException("Invalid quarter: {$value}");
        }

        $this->attributes['quarter'] = $value;
    }

    public function setYearAttribute($value)
    {
        $year = (int) $value;

        if (!$this->isValidYear($year)) {
            throw new \InvalidArgumentException("Invalid year: {$year}");
        }

        $this->attributes['year'] = $year;
    }

    public function setAssessmentDateAttribute($value)
    {
        if (is_string($value)) {
            $this->attributes['assessment_date'] = Carbon::parse($value)->format('Y-m-d');
        } else {
            $this->attributes['assessment_date'] = $value;
        }
    }

    public function setSubmittedAtAttribute($value)
    {
        if (is_string($value)) {
            $this->attributes['submitted_at'] = Carbon::parse($value);
        } else {
            $this->attributes['submitted_at'] = $value;
        }
    }

    // Database migration helper (for reference)
    public static function getMigrationColumns()
    {
        return [
            'assessment_date' => 'Date when the assessment was actually conducted',
            'submitted_at' => 'Timestamp when the assessment was submitted to the system',
            'quarter' => 'Reporting quarter (Q1, Q2, Q3, Q4)',
            'year' => 'Reporting year',
            'district_id' => 'District where assessment was conducted',
            'lga_id' => 'Local Government Area where assessment was conducted',
            'phc_id' => 'Primary Health Center where assessment was conducted'
        ];
    }
}

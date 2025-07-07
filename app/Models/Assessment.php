<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Assessment extends Model
{
    protected $fillable = [
        'assessment_section_id',
        'question',
        'response_type',
        'parent_id',
        'options',
        'conditional_logic',
        'order',
        'assessment_period_id',
        'quarter',
        'year',
        'frequency',
        'custom_interval_days',
        'allow_resubmission',
        'next_available_date',
        'is_final_submission'

    ];

    protected $casts = [
        'options' => 'array',
        'conditional_logic' => 'array',
        'submitted_at' => 'datetime',
        'is_final_submission' => 'boolean',
        'year' => 'integer'
    ];

    public function section()
    {
        return $this->belongsTo(AssessmentSection::class, 'assessment_section_id');
    }

    public function parentQuestion()
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function responses()
    {
        return $this->hasMany(AssessmentResponse::class);
    }

    public function shouldDisplay($parentResponse)
    {
        if (!$this->conditional_logic) {
            return true;
        }

        $logic = $this->conditional_logic;
        return match ($logic['type']) {
            'equals' => $parentResponse == $logic['value'],
            'not_equals' => $parentResponse != $logic['value'],
            default => true
        };
    }

    // Optional: Helper method for options
    public function getOptionValue($key, $default = null)
    {
        return $this->options[$key] ?? $default;
    }

    public function roleCategories()
    {
        return $this->belongsToMany(
            RoleCategory::class,
            'assessment_role_category',
            'assessment_id',
            'role_category_id'
        );
    }
    // In Assessment model
    public function childQuestions()
    {
        return $this->hasMany(Assessment::class, 'parent_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the assessment question
     */
    public function assessment()
    {
        return $this->belongsTo(Assessment::class);
    }

    /**
     * Get the PHC
     */
    public function phc()
    {
        return $this->belongsTo(Phc::class);
    }

    /**
     * Get the district
     */
    public function district()
    {
        return $this->belongsTo(District::class);
    }

    public function lga()
    {
        return $this->belongsTo(Lga::class);
    }


    public function assessmentPeriod()
    {
        return $this->belongsTo(AssessmentPeriod::class);
    }

    public function scopeForPeriod($query, $quarter, $year)
    {
        return $query->where('quarter', $quarter)->where('year', $year);
    }

    public function scopeForPhc($query, $phcId)
    {
        return $query->where('phc_id', $phcId);
    }

    public function scopeFinalSubmissions($query)
    {
        return $query->where('is_final_submission', true);
    }

    public function getDecodedResponseAttribute()
    {
        if (is_string($this->response) && $this->isJson($this->response)) {
            return json_decode($this->response, true);
        }
        return $this->response;
    }

    private function isJson($string)
    {
        json_decode($string);
        return json_last_error() === JSON_ERROR_NONE;
    }

    public function canBeEdited()
    {
        if ($this->is_final_submission) {
            return $this->submitted_at && $this->submitted_at->greaterThan(now()->subWeek());
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
}

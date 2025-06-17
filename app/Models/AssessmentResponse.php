<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AssessmentResponse extends Model
{
    protected $fillable = [
        'user_id',
        'district_id',
        'lga_id',
        'phc_id',
        'assessment_id',
        'response',
        'assessment_period_id',    // MISSING - This is why period ID wasn't saving
        'quarter',                 // MISSING - This is why quarter was NULL
        'year',                    // MISSING - This is why year was NULL
        'additional_response',     // MISSING - For additional responses
        'comments',                // MISSING - For comments
        'submitted_at',            // MISSING - For submission timestamp
        'is_final_submission'      // MISSING - For final submission flag
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
        'is_final_submission' => 'boolean',
        'year' => 'integer'
    ];

    public function responses()
    {
        return $this->hasMany(AssessmentResponse::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function question()
    {
        return $this->belongsTo(Assessment::class);
    }

    public function assessment()
    {
        return $this->belongsTo(Assessment::class);
    }

    public function assessmentPeriod()
    {
        return $this->belongsTo(AssessmentPeriod::class);
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

    public function roleCategories()
    {
        return $this->belongsToMany(RoleCategory::class, 'assessment_role_category', 'assessment_id', 'role_category_id');
    }

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

    private function isJson($string)
    {
        json_decode($string);
        return json_last_error() === JSON_ERROR_NONE;
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Assessment extends Model
{
    protected $fillable = [  'assessment_section_id',
    'question',
    'response_type',
    'parent_id',
    'options',
    'conditional_logic',
    'order',
    'frequency',
        'custom_interval_days',
        'allow_resubmission',
        'next_available_date',
];

        protected $casts = [
            'options' => 'array',
            'conditional_logic' => 'array'
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
        return match($logic['type']) {
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
}

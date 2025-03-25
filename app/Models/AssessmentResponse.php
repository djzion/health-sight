<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AssessmentResponse extends Model
{
    protected $fillable = ['user_id', 'district_id', 'lga_id', 'phc_id', 'assessment_id', 'response'];


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
}

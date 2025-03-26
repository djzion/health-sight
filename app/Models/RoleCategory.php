<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RoleCategory extends Model
{
    protected $fillable = ['role_id', 'assessment_section_id', 'access_level'];

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'role_category_assignments', 'role_category_id', 'role_id');
    }

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function assessmentSection()
    {
        return $this->belongsTo(AssessmentSection::class);
    }

    public function assessments()
    {
        return $this->belongsToMany(
            Assessment::class,
            'assessment_role_category',
            'role_category_id',
            'assessment_id'
        );
    }
}

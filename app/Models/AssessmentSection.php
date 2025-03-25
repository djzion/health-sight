<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AssessmentSection extends Model
{
    protected $fillable = ['name', 'description'];

    // public function assessments()
    // {
    //     return $this->hasMany(Assessment::class)
    //         ->orderBy('order');
    // }


    public function roleCategory() {
        return $this->belongsTo(RoleCategory::class);
    }

    public function role_categories()
    {
        return $this->belongsToMany(RoleCategory::class, 'role_categories');
    }

    public function assessments()
    {
        return $this->hasMany(Assessment::class, 'assessment_section_id');
    }

}
